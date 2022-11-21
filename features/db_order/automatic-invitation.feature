Feature: Automatic invitation to the orders
  As a system
  I want to invite workers as soon as orders arrive
  So that they could work on them

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
      | George     | false       | false         | 0               |
    And The following workers exist:
      | first_name | group  | auto_delivery | available | first_turnaround | next_turnaround |
      | John       | Intake | true          | true      | 360              | 60              |
      | James      | Intake | false         | true      | 360              | 60              |
      | Joana      | Intake | false         | false     | 360              | 60              |
      | Bob        | Review | true          | true      | 360              | 60              |
      | Rob        | Review | false         | false     | 360              | 60              |
    And The following orders exist:
      | title | customer | auto_invite | status    | worked                        | paid  |
      | test1 | Peter    | false       | new       |                               | true  |
      | old1  | Peter    | false       | delivered | John as Intake, Bob as Review | true  |
      | test2 | Kyle     | false       | new       |                               | false |
      | old2  | Kyle     | false       | delivered | James as Intake               | true  |
      | test3 | Pike     | true        | new       |                               | true  |
      | old3  | Pike     | false       | delivered | Joana as Intake               | true  |

  Scenario: New order arrives and there are available workers in the first group, but auto_invite is false
    Given "order test1" is ready for processing
    When System processes "order test1"
    Then "Worker John" should not receive notification for the "order test1" to the "group Intake"
#    And "Admin" should receive notification that "no users could be invited" to the "order test1"

  Scenario: New order arrives (not paid, but customer has delayed payment) and there are available workers in the first group
    Given "order test2" is ready for processing
    When System processes "order test2"
    Then "Worker James" should receive notification for the "order test2" to the "group Intake"

  Scenario: New order arrives and there are available workers in the first group, who didn't work with the customer.
  The one who worked is not available
    Given "order test3" is ready for processing
    When System processes "order test3"
    Then "Worker Joana" should not receive notification for the "order test3" to the "group Intake"
    And "Worker John" should receive notification for the "order test3" to the "group Intake"
    And "Worker James" should receive notification for the "order test3" to the "group Intake"

  Scenario: New order arrives and there are no available workers in the first group. Since no one is available,
  everyone still gets notification
    Given "order test3" is ready for processing
    And "Worker John" is unavailable
    And "Worker James" is unavailable
    When System processes "order test3"
    Then "Worker Joana" should receive notification for the "order test3" to the "group Intake"
    And "Worker John" should receive notification for the "order test3" to the "group Intake"
    And "Worker James" should receive notification for the "order test3" to the "group Intake"

  Scenario: New order arrives and workers who worked with the client receives invitation
    Given "order test2" is ready for processing
    And "Worker John" is available
    And "Worker James" is available
    And "Worker Joana" is available
    When System processes "order test2"
    Then "Worker James" should receive notification for the "order test2" to the "group Intake"
    And "Worker John" should not receive notification for the "order test2" to the "group Intake"
    And "Worker Joana" should not receive notification for the "order test2" to the "group Intake"

  Scenario: Worker rejects invitation. Next time he does not get invitation and only available worker gets it
    Given "order test2" is ready for processing
    And System invited "Worker James" to "group Intake" to "order test2" 5 mins ago
    And "Worker James" rejected invitation to "order test2" for "group Intake" now
    When System processes "order test2"
    And System processes "order test2" once again
    Then "Worker James" should not receive notification for the "order test2" to the "group Intake"
    And "Worker John" should receive notification for the "order test2" to the "group Intake"
    And "Worker Joana" should not receive notification for the "order test2" to the "group Intake"

  Scenario: Invitation is sent and nothing times out.
    Given "order test2" is ready for processing
    And System invited "Worker James" to "group Intake" to "order test2" 10 mins ago
    When System processes "order test2"
    When System processes "order test2" once again
    Then "Worker James" should not receive notification for the "order test2" to the "group Intake"
    And "Worker John" should not receive notification for the "order test2" to the "group Intake"
    And "Worker Joana" should not receive notification for the "order test2" to the "group Intake"

  Scenario: Invitation is sent and timed out
    Given "order test2" is ready for processing
    And System invited "Worker James" to "group Intake" to "order test2" 17 mins ago
    And System processed "order test2" 1 mins ago
    When System processes "order test2" once again
    Then "Worker James" should not receive notification for the "order test2" to the "group Intake"
    And "Worker John" should receive notification for the "order test2" to the "group Intake"
    And "Worker Joana" should not receive notification for the "order test2" to the "group Intake"

  @skip
  Scenario: Order didn't have autoinvite enabled when the system tried to process it, but then it was enabled
    Given "order test1" is ready for processing
    And System processed "order test1" now
    When Admin enables autoinvite for "order test1"
    And System processes "order test1" now
    Then "Worker John" should receive notification for the "order test1" to the "group Intake"