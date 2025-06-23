---
post_title: WooCommerce Release Schedule
sidebar_label: Release Schedule
---

# WooCommerce Release Schedule

The schedule can be found [here](https://developer.woocommerce.com/release-calendar/): the page also explains the types 
of events in the calendar like `Releases`, `Release Candidates (RC)` and `Feature Freeze Dates`.

While the calendar reflects the events made public, there are specific steps in the release process that are internal, 
and this page aims to provide necessary context about those events for release leads and other involved parties.


## Detailed release schedule

This section will use the publicly available release schedule as anchors and clarify where the internal events fit in.

### Feature Freeze (start of the release cycle)

This step is mostly automated, and in nut-shell, it creates a dedicated release branch where the future release undergoes testing and stabilization.
At this point, the Developer Advocacy team publishes pre-release updates ([example](https://developer.woocommerce.com/2025/05/12/woocommerce-9-9-pre-release-updates/))

### RC1 (Feature Freeze + 1 week)

This step is where various internal testing processes are happening: regression testing with canonical extensions,
regression testing in multiple environments, and exploration testing (incl. by the contributing teams).

### RC2 (RC1 + 2 weeks)

TODO: not sure, thought is where we communicate RC availability for testing by community, but not sure.

### Final Release (RC2 + 1 week)

The final release process includes an additional staging step when it is not marked as stable yet and is deployed to our staging infrastructure.
If the staging step reveals critical issues, a dot-release will be created and follow the same staging procedure. Once staging is successful, the 
release is marked as stable and becomes available to everyone.

At this point, the Developer Advocacy team publishes prepared in-advance release highlights ([example](https://developer.woocommerce.com/2025/06/09/woocommerce-9-9-its-fast-period/))

## Delays

Due to business needs, the release dates may be subject to change. Here are some hints on approaching and effectively wrangling this situation.

Once the need for changes in the release schedule is confirmed, create an internal post and communicate the necessary details.
This post provides an opportunity for teams to share additional context, which may help invalidate and correct the schedule changes.

Once the post feedback and release schedule changes have cleared:

- ask the Developer Advocacy team to communicate the changes publicly ([example](https://developer.woocommerce.com/2025/06/02/woocommerce-9-9-release-is-delayed/))
- update [the calendar](https://developer.woocommerce.com/release-calendar/) with the new release dates

> Note: To minimize friction for teams, it's recommended not to change intervals between RCs and the final release but 
> instead to shift the entire release cycle. The intervals consider multiple factors, including the capacity across teams.
