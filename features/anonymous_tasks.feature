@anonymous_task
Feature: Anonymous tasks
  In order to manage all the tasks
  As a fully authenticated admin
  I need to be able to edit or delete anonymous tasks

  @delete @admin
  Scenario: Delete an existing task as a fully authenticated admin
    Given I am authenticated as "testadmin" "mdp"
    And I am on "/tasks"
    And I should see a task "test_task_6 title - anonymous" with its content "test_task_6 content - anonymous"
    When I press "task-6-delete-btn"
    Then I should not see a task "test_task_6 title - anonymous"

  @edit @admin
  Scenario: Edit a task created by an anonymous user as a fully authenticated admin
    Given I am authenticated as "testadmin" "mdp"
    And I am on "/tasks"
    And I follow "test_task_7 title - anonymous"
    When I fill in "task_title" with "test_task_7 title - anonymous - modified"
    And I fill in "task_content" with "test_task_7 content - anonymous - modified"
    And I press "Modifier"
    Then I should be on "/tasks"
    And I should see a task "test_task_7 title - anonymous - modified" with its content "test_task_7 content - anonymous - modified"

  @edit @user
  Scenario: Try to edit a task created by an anonymous user
    Given I am authenticated as "testuser" "mdp"
    And I am on "/tasks"
    And I follow "test_task_4 title - anonymous"
    Then the response status code should be "403"

  @delete @user
  Scenario: Try to delete a task created by an anonymous user
    Given I am authenticated as "testuser" "mdp"
    And I am on "/tasks"
    And I press "task-5-delete-btn"
    Then the response status code should be "403"
