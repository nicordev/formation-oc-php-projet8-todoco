# Technical documentation

## Symfony general behaviour

Every incoming HTTP request is redirected to the front controller located in `public/index.php` whose role is to boot the Symfony kernel, analyze the request, ask the appropriate controller to build the response and send this response back to the user.

Here is what the front controller does in detail:
1. Call the file `config/bootstrap.php` to load the environment variables set in the `.env.local` file (or `.env` if there is no `.env.local` file) located at the root of the project folder.
2. Instantiate a `App\Kernel $kernel` object regarding to the chosen environment via the setting `APP_ENV=environnementNameHere` written in the `.env.local` file.
3. Instantiate a  `Symfony\Component\HttpFoundation\Request $request` object containing all the request data like the HTTP method used, the URL or the superglobals.
4. Pass the `$request` instance to the `$kernel` to build a `Symfony\Component\HttpFoundation\Response $response` object containing the headers and the body of the response
5. Send the response via `$response->send()` which will internally call the PHP instructions `header` and `echo`
6. Execute the last `$kernel` operations before its closure.

During this process, a series of useful events will be dispatched, on which we can hook some listeners.

## Zoom in authentication

Every user of a Symfony application could be either anonymous or authenticated.

### Prerequisites

A class implementing the `Symfony\Component\Security\Core\User\UserInterface` interface should be implemented to represent users.

### Configuration

The `security.yaml` file contains every parameter linked to the authentication.

Here is the one from the application with some useful comments:

```yaml
security:
    encoders:
        App\Entity\User: bcrypt # Encoder used to hash passwords

    providers:
        doctrine:
            entity:
                class: 'App\Entity\User' # Class used to represent a user. It should implement the Symfony\Component\Security\Core\User\UserInterface interface
                property: 'username' # Property of the above class used for authentication
        in_memory: { memory: null }

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            anonymous: ~
            pattern: ^/
            form_login:
                login_path: login # URI used to reach the login page
                check_path: login_check # URI used by the authentication form located in the login page
                always_use_default_target_path:  true
                default_target_path:  / # Once the user is successfully authenticated, it will be redirected to this URI
            logout: ~

    access_control: # Limit access by URI
        - { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY } # Here everybody can use this URI
        - { path: ^/users, roles: ROLE_ADMIN } # Only authenticated users having the ROLE_ADMIN role can access pages which URI begin with /users
        - { path: ^/, roles: ROLE_USER } # Only authenticated users can access pages which URI begin with / (because every authenticated user has the ROLE_USER role)

    role_hierarchy:
        ROLE_USER: ~ # The ROLE_USER role does not contain another role (it is still present here to be able to show it in the user creation form)
        ROLE_ADMIN: ROLE_USER # The ROLE_ADMIN role contains the ROLE_USER role, which means that a user having the ROLE_ADMIN role will be granted the ROLE_USER too
```

### Authentication

To be authenticated, an anonymous user must go to `/login`, enter his credentials (user name and password) and submit the form.

A `POST` request containing the user credentials will be sent on `/login_check` to be handled by the front controller.

When the `kernel.request` is dispatched, the firewall `Symfony\Component\Security\Http\Firewall` will proceed to the authentication through its `Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener` listener. It will:
1. Check the CSRF token of the form if there is any.
2. Call its `Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager` to:
    1. Save the user name from the authentication form in the session variable (`$_SESSION`) with the key `_security.last_username`.
    2. Fetch the user from the database by using the user name sent in the authentication form.
    3. Check if the user account is not blocked, deactivated or expired.
    4. Check that the password from the authentication form matches the user's password stored in the database by using the encoder set in the `security.yaml` file.
    5. Put the fetched user in a `Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken $authenticatedToken` token.
    6. Dispatch a `security.authentication.success` event if everything went smoothly or a `security.authentication.failure` event if there was a problem.
3. Create a redirection response to the homepage (given the URI `/` specified in the `security.yaml` file) if the authentication succeeded.

The authenticated user data are then placed in a JSON token called *security token* stored in the session with the key `_security_nameOfTheFirewallHere` by the `Symfony\Component\Security\Http\Firewall\ContextListener` listener since the `kernel.response` event is dispatched.

A new request is then handled to reach the home page where the authenticated user's data are extract from the security token stored in the session.

### Authenticated

Once the user fully authenticated, he can then access all routes authorized by its role thanks to the token stored in the session. Once the session is destroyed, the user will have to authenticate himself again to access the protected content.

A first line of check is then executed regarding the requested URI, defined under the key `access_control` in the `security.yaml` file:

```yaml
security:
    # ...
    access_control:
        - { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/users, roles: ROLE_ADMIN }
        - { path: ^/, roles: ROLE_USER }
```

Here we can see that:
* anonymous users are only allowed to use the URI `/login`,
* only authenticated users who own the `ROLE_ADMIN` can access routes which URI begin with `/users`,
* authenticated users who only own the `ROLE_USER` role can access all routes except those reserved to the `ROLE_ADMIN` role described on the previous line.

Note that users owning the `ROLE_ADMIN` can also access URI reserved for the `ROLE_USER` role thanks to the configuration of the role hierarchy in the `security.yaml` file here:

```yaml
security:
    # ...
    role_hierarchy:
        ROLE_USER: ~
        ROLE_ADMIN: ROLE_USER
```

When the access control by URI is not enough, for instance when a user without the admin role tries to edit or delete a task created by another user, we use the Symfony voter system by calling the `denyAccessUnlessGranted` method of the `Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait` trait.

This method makes it possible to use custom logic to deny access using special objects called *voters*. If the user is denied, then a 403 response will be sent.

Here is an example coming from the `deleteTaskAction` method of the `App\Controller\TaskController` controller:

```php
    /**
     * @Route("/tasks/{id}/delete", name="task_delete") // This route is accessible to all authenticated users
     */
    public function deleteTaskAction(Task $task)
    {
        $this->denyAccessUnlessGranted(TaskVoter::DELETE, $task); // Call the denyAccessUnlessGranted() method asking every voter supporting the TaskVoter::DELETE attribute and using a Task entity as a subject

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
```

1. The `denyAccessUnlessGranted()` method will ask the `Symfony\Component\Security\Core\Authorization\AuthorizationChecker` to check if the user is authorized by calling its `isGranted()` method.
2. `AuthorizationChecker::isGranted()` will then ask his `Symfony\Component\Security\Core\Authorization\AccessDecisionManager` to interrogate every available voters using its `decide()` method
3. `AccessDecisionManager::decide()` will then call the `vote()` of every voters (like our `App\Security\TaskVoter` voter) to:
    1. know if the voter is competent by calling the voter's `support()` method
    2. interrogate the voter by calling its `voteOnAttribute()` method (only if the voter is competent)

If at least one competent voter granted the access (which means if the `voteOnAttribute()` method of the voter returns true), then the user is authorized and the remaining lines of code of the controller method can be executed. On the contrary, if none voter grants access, then a 403 response will be sent to the user.

This behavior is defined by the voting strategy used. In our case, the default strategy called `affirmative` is used.

> Note that the strategy used can be set in the `security.yaml` that way:
>
> ```yaml
> security:
>     # ...
>     access_decision_manager:
>         strategy: affirmative # Strategy used, "affirmative" by default
> ```

### Log out

Once the authenticated user clicks on "Se déconnecter", a request is sent on `/logout`. Then, when the `kernel.request` is dispatched, the `Symfony\Component\Security\Http\Firewall\LogoutListener` listener of the firewall will disconnect the user by deleting the current session and creating a new one by calling the PHP function `session_regenerate_id()`.

After that, a redirection to the homepage is launched. As the user is now anonymous, he is redirected to the login page.

