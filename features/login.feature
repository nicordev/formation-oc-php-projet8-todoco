Feature: Login
  In order to access to the task list
  As an anonymous user
  I need to login

  Scenario: Login
    Given I am on the login page
    When I fill the login form and submit it
    Then I am redirected to the homepage