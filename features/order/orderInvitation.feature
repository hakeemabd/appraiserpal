Feature: Manual invitations to orders
  As a system administrator
  I want to invite users to work on orders
  So that they can work on them

  Background:
    Given There is a new order id=11
    And Order 11 customer is "Peter" (100)
    And There the following groups exist:
      | id | name   | sort |
      | 1  | Intake | 1    |
      | 2  | Review | 2    |
    And The following workers exist:
      | id | name  | group  | worked_with_customer |
      | 1  | John  | Intake | 100                  |
      | 2  | James | Intake | 10                   |
      | 3  | Bob   | Review | 100                  |
      | 4  | Rob   | Review | 50                   |

  Scenario: Admin invites workers who worked with this client before
  Each group has such worker
    When Admin invites experienced workers from group "Intake" to order 11
    And Admin invites experienced workers from group "Review" to order 11
    Then Worker "Bob" should receive notification for the order 11 to the group "Review"
    And Worker "John" should receive notification for the order 11 to the group "Intake"

  Scenario: Admin invites workers who worked with this client before
  Only Intake group has such worker
    Given Order 11 customer is "Peter" (10)
    When Admin invites experienced workers from group "Intake" to order 11
    And Admin invites experienced workers from group "Review" to order 11
    Then Worker "Bob" should not receive notification for the order 11 to the group "Review"
    And Worker "Rob" should not receive notification for the order 11 to the group "Review"
    And Worker "James" should receive notification for the order 11 to the group "Intake"
    And Worker "John" should not receive notification for the order 11 to the group "Intake"
