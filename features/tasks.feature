Feature: Tasks
  In order to manage tasks
  As a fully authenticated user
  I need to see all created tasks on a page and manage my tasks

  Scenario: Check homepage
    Given I am authenticated
    And I am on "/"
    Then I should see a "link" "Se déconnecter"
    And I should see a "link" "Créer une nouvelle tâche"
    And I should see a "link" "Consulter la liste des tâches à faire"
    And I should see a "link" "Consulter la liste des tâches terminées"

  Scenario: Show the task list from the homepage
    Given I am authenticated
    And I am on "/"
    When I follow "Consulter la liste des tâches à faire"
    Then I should be on "/tasks"
    And I should see every tasks

  Scenario: Reach the task creation page from the homepage
    Given I am authenticated
    And I am on "/"
    When I follow "Créer une nouvelle tâche"
    Then I should be on "/tasks/create"

  Scenario: Reach the task creation page from the task list
    Given I am authenticated
    And I am on "/tasks"
    When I follow "Créer une tâche"
    Then I should be on "/tasks/create"

  Scenario: Create a new task
    Given I am authenticated
    And I am on "/tasks/create"
    When I fill in "task_title" with "New test task title"
    And I fill in "task_content" with "New test task content"
    And I press "Ajouter"
    Then I should be on "/tasks"
    And I should see the task "New test task title" with its content "New test task content"

  Scenario: Edit an existing task
    Given I am authenticated
    And I am on "/tasks"
    And I follow "New test task title"
    When I fill in "task_title" with "New test task title - modified"
    And I fill in "task_content" with "New test task content - modified"
    And I press "Modifier"
    Then I should be on "/tasks"
    And I should see the task "New test task title - modified" with its content "New test task content - modified"