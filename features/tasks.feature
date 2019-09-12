Feature: Task list
  In order to see all created tasks
  As a fully authenticated user
  I need to see all created tasks on a page

  Scenario: Show the task list
    Given the user is connected
    And the user is on the home page
    When the user click on the button to show the task list
    Then the user is redirected to the task list