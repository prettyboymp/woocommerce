#!/usr/bin/env bash

readarray -t repositories < manual-ci-bulk-run-spike.txt

# Sort out which repositories provide the necessary workflows first.
filtered=()
skipped=()
echo -n "Looking up repositories (${#repositories[@]}): "
for repository in ${repositories[@]}; do
	match=$( gh workflow list --json path --jq '.[].path' --repo $repository | grep -E '.github/workflows/(manual-ci.yml|ci-manual.yml)' | wc -l )
	if [[ $match == '1' ]]; then
		filtered+=( $repository )
	else
		skipped+=( $repository )
	fi
	echo -n '.'
done
filtered=( $( printf '%s\n' "${filtered[@]}" | sort ) )
skipped=( $( printf '%s\n' "${skipped[@]}" | sort ) )
echo ''

# Report the skipped repositories.
echo "Skipping due to missing target workflows (${#skipped[@]} repo(s))"
for repository in ${skipped[@]}; do
	echo "    -- $repository"
done

# Run checks for the target repositories.
running=()
echo "Launching checks (${#filtered[@]} repo(s))"
for repository in ${filtered[@]}; do
	echo -n "    -- $repository:"

	# Report identified workflow details.
	workflow=$( gh workflow list --json path,id --repo $repository | jq --compact-output '.[]' | grep -E '.github/workflows/(manual-ci.yml|ci-manual.yml)' )
	workflow_path=$( echo $workflow | jq --raw-output '( .path )' )
	workflow_id=$( echo $workflow | jq --raw-output '( .id )' )
	echo -n " workflow ${workflow_path}(${workflow_id})"

	# Report last run details.
	previous_run=$( gh api -H "Accept: application/vnd.github+json" -H "X-GitHub-Api-Version: 2022-11-28" /repos/${repository/"https://github.com/"/}/actions/workflows/${workflow_id}/runs?per_page=1 --jq '.workflow_runs.[].id' )
	echo -n " previous run #${previous_run} "

	# Start a new run and report back.
	echo '{"wc-version":"9.9.0-rc.1", "qit-tests":"WooCommerce Pre-Release Tests (includes Activation, WooCommerce E2E and API tests)"}' | gh workflow run ${workflow_id} --json --repo $repository >/dev/null 2>&1
	for i in {1..10}; do
	    echo -n '.' && sleep 1s
	    last_run=$( gh api -H "Accept: application/vnd.github+json" -H "X-GitHub-Api-Version: 2022-11-28" /repos/${repository/"https://github.com/"/}/actions/workflows/${workflow_id}/runs?per_page=1 --jq '.workflow_runs.[].id' )
	    if [[ $last_run != $previous_run ]]; then
	    	running+=( "$repository;${last_run}" )
	    	echo -n " running #${last_run}"
	    	break
	    fi
	done

	echo ''
done

echo "Waiting for completion (${#running[@]} run(s), 1m check interval): "
while [ ${#running[@]} -gt 0 ]; do
	echo -n '.' && sleep 1m
	updated=()
	for entry in ${running[@]}; do
		fragments=( ${entry//;/ } )
		repository=${fragments[0]}
		id=${fragments[1]}
		status=$( gh api -H "Accept: application/vnd.github+json" -H "X-GitHub-Api-Version: 2022-11-28" /repos/${repository/"https://github.com/"/}/actions/runs/$id --jq '.status' )
		if [[ $status == 'completed' ]] || [[ $status == 'failure' ]] || [[ $status == 'startup_failure' ]]; then
			echo -n "*($status)"
		else
			updated+=( $entry )
		fi
	done
	running=( "${updated[@]}" )
done
