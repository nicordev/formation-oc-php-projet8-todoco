@security
Feature: Access control
  In order to see the task list and manage my tasks
  As an anonymous user
  I need to login first

  Scenario: Check deny access to the homepage for anonymous user
    Given I am on "/"
    Then I should be on "/login"

  Scenario: Check deny access to the task list for anonymous user
    Given I am on "/tasks"
    Then I should be on "/login"

  Scenario: Check deny access to the task creation page for anonymous user
    Given I am on "/tasks/create"
    Then I should be on "/login"

  Scenario: Check login page
    Given I am on "/login"
    Then I should see a "button" named "Se connecter"
    And I should see a "field" named "username"
    And I should see a "field" named "password"

  @login
  Scenario: Login
    Given I am on "/login"
    When I fill in "username" with "bob"
    And I fill in "password" with "mdp"
    And I press "Se connecter"
    Then I should be on "/"
    And I should see a "link" named "Se déconnecter"

  @logout
  Scenario: Logout
    Given I am authenticated
    When I follow "Se déconnecter"
    Then I should be on "/login"
    And I should see a "button" named "Se connecter"