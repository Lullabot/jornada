# Jornada

_A PHP library and CLI tool to calculate your team's working days_

## Example

```shell script
$ bin/console help member:report
Description:
  Generate a report of working days for team members

Usage:
  member:report [options] [--] <end-date> <people>

Arguments:
  end-date                       The end date to calculate the report to, in YYYY-MM-DD format.
  people                         A list of people on the project, one per line.

Options:
  -b, --booked-pto[=BOOKED-PTO]  Path to booked PTO CSV with columns <person>,<day>. [default: ""]
  -o, --owed-pto[=OWED-PTO]      Path to owed PTO CSV with columns <person>,<type>,<day>. [default: ""]
  -s, --start-date=START-DATE    The start date to calculate the report from, in YYYY-MM-DD format. [default: "2020-12-02"]
  -h, --help                     Display this help message
  -q, --quiet                    Do not output any message
  -V, --version                  Display this application version
      --ansi                     Force ANSI output
      --no-ansi                  Disable ANSI output
  -n, --no-interaction           Do not ask any interactive question
  -v|vv|vvv, --verbose           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

$ bin/console member:report --start-date=2020-11-30 2020-12-31 tests/fixtures/csv/multiple/people.csv
Team business days: 96
andrew has 24 business days remaining, 24 working days remaining, finishing on 2020-12-31.
amanda has 24 business days remaining, 24 working days remaining, finishing on 2020-12-31.
harry has 24 business days remaining, 24 working days remaining, finishing on 2020-12-31.
zoe has 24 business days remaining, 24 working days remaining, finishing on 2020-12-31.
Total team working days: 96

$ bin/console member:report --start-date=2020-11-30 2020-12-31 --booked-pto=tests/fixtures/csv/multiple/booked-pto.csv tests/fixtures/csv/multiple/people.csv
Team business days: 96
andrew has 24 business days remaining, 23 working days remaining, finishing on 2020-12-31.
amanda has 24 business days remaining, 21 working days remaining, finishing on 2020-12-31.
harry has 24 business days remaining, 23 working days remaining, finishing on 2020-12-31.
zoe has 24 business days remaining, 23 working days remaining, finishing on 2020-12-30.
Total team working days: 90

$ bin/console member:report --start-date=2020-11-30 2020-12-31 --booked-pto=tests/fixtures/csv/multiple/booked-pto.csv --owed-pto=tests/fixtures/csv/multiple/owed-pto.csv tests/fixtures/csv/multiple/people.csv
Team business days: 96
andrew has 24 business days remaining, 22 working days remaining, finishing on 2020-12-30.
amanda has 24 business days remaining, 19 working days remaining, finishing on 2020-12-29.
harry has 24 business days remaining, 20 working days remaining, finishing on 2020-12-25.
zoe has 24 business days remaining, 19 working days remaining, finishing on 2020-12-25.
Total team working days: 80
```

## Key APIs

* [WorkingDaysCalculator](src/WorkingDaysCalculator.php) supports calculating business days, working days, and end dates for arbitrary ranges of dates based on existing and future holiday plans.
* [TeamCalculator](src/TeamCalculator.php) supports grouping multiple WorkingDaysCalculators together, and then calculating results for an entire team.
