Feature: Manual invitations to orders
  As a system administrator
  I want to invite users to work on orders
  So that they can work on them

  Background:
    Given The following groups exist:
      | name   | sort |
      | Intake | 1    |
      | Review | 2    |
    And The following customers exist:
      | first_name | auto_invite | auto_delivery | delayed_payment |
      | Peter      | false       | false         | 0               |
      | Kyle       | true        | false         | 10              |
      | Pike       | false       | false         | 0               |
    And The following workers exist:
      | first_name | group  | auto_delivery | available | first_turnaround | next_turnaround |
      | John       | Intake | true          | true      | 360              | 60              |
      | James      | Intake | false         | true      | 360              | 60              |
      | Joana      | Intake | false         | false     | 360              | 60              |
      | Bob        | Review | true          | true      | 360              | 60              |
      | Rob        | Review | false         | false     | 360              | 60              |
    And The following orders exist:
      | title | customer | auto_invite | status    | worked                        |
      | test1 | Peter    | false       | new       |                               |
      | test2 | Kyle     | false       | new       |                               |
      | test3 | Pike     | false       | new       |                               |
      | old1  | Peter    | false       | delivered | John as Intake, Bob as Review |
      | old2  | Kyle     | false       | delivered | James as Intake               |
      | old3  | Pike     | false       | delivered | Joana as Intake               |

  Scenario: Admin invites workers who worked with this client before
  Each group has such worker
    When Admin invites experienced and available workers from "group Intake" to "order test1"
    And Admin invites experienced and available workers from "group Review" to "order test1"
    Then "Worker Bob" should receive notification for the "order test1" to the "group Review"
    And "Worker John" should receive notification for the "order test1" to the "group Intake"

  Scenario: Admin invites workers who worked with this client before
  Only Intake group has such worker
    When Admin invites experienced and available workers from "group Intake" to "order test2"
    Then "Worker James" should receive notification for the "order test2" to the "group Intake"
    And "Worker John" should not receive notification for the "order test2" to the "group Intake"

  Scenario: Admin invites workers who worked with this client before
  Intake group has such worker, but she is not available
    When Admin invites experienced and available workers from "group Intake" to "order test3"
    Then "Worker Joana" should not receive notification for the "order test2" to the "group Intake"

  Scenario: Admin invites all available workers from the Review group
  Review group has one worker available, another is not
    When Admin invites available workers from "group Review" to "order test1"
    Then "Worker Bob" should receive notification for the "order test1" to the "group Review"
    Then "Worker Rob" should not receive notification for the "order test1" to the "group Review"

  Scenario: Admin invites all workers from the Review group
  Both workers should receive an invitation
    When Admin invites all workers from "group Review" to "order test1"
    Then "Worker Bob" should receive notification for the "order test1" to the "group Review"
    Then "Worker Rob" should receive notification for the "order test1" to the "group Review"

  Scenario: Admin invites all workers from all groups
    When Admin invites all workers from "group Review" to "order test1"
    And Admin invites all workers from "group Intake" to "order test1"
    Then "Worker Bob" should receive notification for the "order test1" to the "group Review"
    Then "Worker Rob" should receive notification for the "order test1" to the "group Review"
    Then "Worker Joana" should receive notification for the "order test1" to the "group Intake"
    Then "Worker James" should receive notification for the "order test1" to the "group Intake"
    Then "Worker John" should receive notification for the "order test1" to the "group Intake"