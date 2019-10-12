# How to contribute?

You saw a bug? You thought of an awesome feature that could be added to the application? Then you can contribute by either sending issues or submitting pull requests.

## Send an issue

With issues you just highlight a bug or propose improvments.

To do so, you'll have to go to the issue page (located here: https://github.com/nicordev/formation-oc-php-projet8-todoco/issues) and do the following steps:
1. Click on *"New issue"*.
2. Write a clear and concise title for your issue, like *"Missing translation on the task creation page"*.
3. Give as much detail as possible in the issue comment. For instance:
    ```
    On the task creation page, the labels title and content are not translated.
    ```
4. Choose a label, in this example it should be *bug*.
5. Click on *"Submit new issue"*.

> For instance, here is how the issue *"Missing translation on the task creation page"* could look like:
> 
> ```
> On the task creation page /tasks/create, the labels title and content are not translated.
> 
> Is it possible to have these labels translated?
> ```

Last tip: if you have more than one topic to expose, please do one issue per topic. For instance, if you see a missing translation and a broken link, do one issue for the missing translation and another one for the link.

## Submit a pull request

With pull requests however, you can actually submit an improvement, a bug correction or a new feature by sending your homemade code.

To do so, first clone the project by executing `git clone urlOfTheRepository` in your project folder, where `urlOfTheRepository` can be found by clicking on *"Clone or download"* on the github repository (here: https://github.com/nicordev/formation-oc-php-projet8-todoco)

Create a new branch and start coding whatever you want to add to the project in that branch, then execute `git push origin nameOfYourBranchHere` to send the branch to the GitHub repository when you're done.

Last step, go to `urlOfTheRepository/pull/new/nameOfYourBranchHere` to submit your pull request.

You'll be asked to add a title to your pull request, so as for issues, please write a clear and concise title showing the purpose of your pull request, for instance *"Add missing translation on the task creation page"*.

Also describe your work in the comment section and refer to the issue you wanted to cover by using the *#code* of the issue, like this: `Will close #46`.

> Here is an example of how the pull request *"Add missing translation on the task creation page"* could be described in the comment section:
>
> ```
> Will close #46
> 
> The title and content labels located on the task creation page /tasks/create were not translated.
> 
> This pull request adds a translation in french for those labels.
> ```

## Last note

Any help will be greatly appreciated. So if you do help us, then **thank you for contributing to this awesome project!**