@task
Feature: Tasks
  In order to manage tasks
  As a fully authenticated user
  I need to see all created tasks on a page and manage my tasks

  @navigate @user
  Scenario: Check homepage
    Given I am authenticated as "testuser" "mdp"
    And I am on "/"
    Then I should see a "link" named "Se déconnecter"
    And I should see a "link" named "Créer une nouvelle tâche"
    And I should see a "link" named "Consulter la liste des tâches à faire"
    And I should see a "link" named "Consulter la liste des tâches terminées"

  @navigate @user
  Scenario: Show the task list from the homepage
    Given I am authenticated as "testuser" "mdp"
    And I am on "/"
    When I follow "Consulter la liste des tâches à faire"
    Then I should be on "/tasks"
    And I should see every tasks

  @navigate @user
  Scenario: Reach the task creation page from the homepage
    Given I am authenticated as "testuser" "mdp"
    And I am on "/"
    When I follow "Créer une nouvelle tâche"
    Then I should be on "/tasks/create"

  @navigate @user
  Scenario: Reach the task creation page from the task list
    Given I am authenticated as "testuser" "mdp"
    And I am on "/tasks"
    When I follow "Créer une tâche"
    Then I should be on "/tasks/create"

  @create @user
  Scenario: Create a new task
    Given I am authenticated as "testuser" "mdp"
    And I am on "/tasks/create"
    When I fill in "task_title" with "New test task title"
    And I fill in "task_content" with "New test task content"
    And I press "Ajouter"
    Then I should be on "/tasks"
    And I should see a task "New test task title" with its content "New test task content"

  @delete @user
  Scenario: Delete an existing task
    Given I am authenticated as "testuser" "mdp"
    And I am on "/tasks"
    And I should see a task "test_task_1 title" with its content "test_task_1 content"
    When I press "Supprimer"
    Then I should not see a task "test_task_1 title"

  @edit @user
  Scenario: Edit an existing task
    Given I am authenticated as "testuser" "mdp"
    And I am on "/tasks"
    And I follow "test_task_2 title"
    When I fill in "task_title" with "test_task_2 title - modified"
    And I fill in "task_content" with "test_task_2 content - modified"
    And I press "Modifier"
    Then I should be on "/tasks"
    And I should see a task "test_task_2 title - modified" with its content "test_task_2 content - modified"

  @edit @user
  Scenario: Try to edit a task created by another user
    Given I am authenticated as "testuser" "mdp"
    And I am on "/tasks"
    And I follow "test_task_3 title - added by testadmin"
    Then the response status code should be "403"

  @delete @user
  Scenario: Try to delete a task created by another user
    Given I am authenticated as "testuser" "mdp"
    And I am on "/tasks"
    And I press "task-3-delete-btn"
    Then the response status code should be "403"