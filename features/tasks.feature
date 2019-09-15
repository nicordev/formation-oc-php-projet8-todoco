Feature: Tasks
  In order to manage tasks
  As a fully authenticated user
  I need to see all created tasks on a page and manage my tasks

  Scenario: Show the task list
    Given I am authenticated
    And I am on the homepage
    When I click on the link to show the task list
    Then I am redirected to the task list

