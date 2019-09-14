Feature: Login
  In order to access to the task list
  As an anonymous user
  I need to login

  Scenario: Login
    Given the anonymous user is on the login page
    When the user fill the login form and submit it
    Then the user is redirected to the homepage