Feature: Login
  In order to see the task list and manage my tasks
  As an anonymous user
  I need to login

  Scenario: Login
    Given I am on the login page
    When I fill the login form and submit it
    Then I am redirected to the homepage fully authenticated

  Scenario: Logout
    Given I am authenticated
    When I click on the link to logout
    Then I am redirected to the login page as an anonymous user