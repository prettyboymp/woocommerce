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
echo "Skipping due to missing target workflows (${#skipped[@]} repos)"
for repository in ${skipped[@]}; do
	echo "    -- $repository"
done

# Run checks for the target repositories.
echo "Launching checks (${#filtered[@]} repos)"
for repository in ${filtered[@]}; do
	echo "    -- $repository"
done
