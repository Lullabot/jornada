# Jornada

_A PHP library and CLI tool to calculate your team's working days_

## Example

```shell script
$ bin/console help member:report
  Description:
    Generate a report of working days for team members

  Usage:
    member:report [options] [--] <end-date>

  Arguments:
    end-date                       The end date to calculate the report to, in YYYY-MM-DD format.

  Options:
    -b, --booked-pto[=BOOKED-PTO]  Path to booked PTO CSV with columns <person>,<day>. [default: "./booked-pto.csv"]
    -o, --owed-pto[=OWED-PTO]      Path to owed PTO CSV with columns <person>,<type>,<day>. [default: "./owed-pto.csv"]
    -s, --start-date=START-DATE    The start date to calculate the report from, in YYYY-MM-DD format. [default: "2020-09-04"]
    -h, --help                     Display this help message
    -q, --quiet                    Do not output any message
    -V, --version                  Display this application version
        --ansi                     Force ANSI output
        --no-ansi                  Disable ANSI output
    -n, --no-interaction           Do not ask any interactive question
    -v|vv|vvv, --verbose           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

$ bin/console member:report 2020-12-31 --booked-pto=../booked-pto.csv --owed-pto=../owed-pto.csv
  Team business days: 340
  Jane has 85 business days remaining, 64 working days remaining, finishing on 2020-10-29.
  Bob has 85 business days remaining, 72 working days remaining, finishing on 2020-10-21.
  Frank has 85 business days remaining, 64 working days remaining, finishing on 2020-10-29.
  Jessica has 85 business days remaining, 71 working days remaining, finishing on 2020-10-22.
  Total team working days: 271

```

## Key APIs

* [WorkingDaysCalculator](src/WorkingDaysCalculator.php) supports calculating business days, working days, and end dates for arbitrary ranges of dates based on existing and future holiday plans.
* [TeamCalculator](src/TeamCalculator.php) supports grouping multiple WorkingDaysCalculators together, and then calculating results for an entire team.
