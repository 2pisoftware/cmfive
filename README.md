Cmfive
======

Cmfive is a php framework for creating robust and extensible business applications.

It started as a micro framework which was developed by Carsten Eckelmann in 2007 in Sydney on the bus to work, 
lay dormant for years until it re-emerged as the foundation to the Flow Business System (https://github.com/PyramidPower/flow),
which was developed in house to run a 70 people Solar Installation company.

cmFive grew from the codebase of Flow, but has since then been shaped to be more modern, slimmer and ready to take
on other business applications.

License
=======

Cmfive Copyright (C) 2014 2pi Software, NSW, Australia

original Flow source code Copyright (C) 2012 Pyramid Power Group, NSW, Australia

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

# cmFive Wiki

# CmFive Quickstart
## Docker
The easiest way to get up and running is to use the docker image. Just fire up kitematic and search for cmfive.

The image is built using [this github repository](https://github.com/syntithenai/docker-cmfive) which provides a cmfive install script as part of it's Dockerfile and extensive help on working with the image.

## Manual Installation instructions
1. create a mysql database, eg. cmfive
2. Cmfive must be installed at the root of a domain. 
	2.1. Create a vhost entry for apache, eg.
<VirtualHost *:80>
    DocumentRoot "C:\Users\admin\git\cmfive"
    ServerName cmfive.local
    ErrorLog "logs/cmfive.localhost-error.log"
    CustomLog "logs/cmfive.localhost-access.log" combined
    <Directory "C:\Users\admin\git\cmfive">
      Options FollowSymLinks
      AllowOverride All
      Order allow,deny
      Allow from all
    </Directory>
</VirtualHost>
	2.2 Create a host entry in C:/Windows/System32/drivers/etc/hosts
        127.0.0.1    cmfive.local
3. go to http://cmfive.local/install 


# CmFive Features
## The main features of CmFive are

- Written in PHP, the most used programming language of the web.
- Modular Architecture
- System Modules
- Custom Modules
- Model, View and Controller architecture
- Object-Relational framework
- Global layout
- Easy to use Html helper library
- Built-in core functionality
- Role Based Access Control
- Roles, Users, Groups
- Help System
- Fulltext Search
- Task Tracking and Custom Workflows
- Internal Messaging and Notifications
- Report Builder
- Wiki
- Auditing
- Pragmatic mix of function library, file naming conventions and OOP
- Aspect Oriented Programming Concepts
- For storing object modification data
- For creating fulltext index entry for objects
- For accessing objects via RESTful URIs
- Loose Coupling via Listeners and Hooks

## For Developers
- Rapid Application Development
- MVC framework with models, controllers (actions), templates, layouts, partials
- Modular architecture with per module configuration, sub modules
- Loose coupling
- Automatic url routing
- Pre-listeners, post-listeners
- Hooks
- Object-Relational database abstraction with out of the box CRUD operations
- Aspect-Oriented design for
 - search indexing
 - logging
 - versioning
- RBAC (Role based access control) on a per module level
- HTML utility library for rapid creation of forms, pagination, etc.
- Built in Help System
 - Help files per module
 - Context sensitive automatic display of help document
 - Help content can have sections which only display for certain user groups
- Built in Full-Text Search
 - Every object can be indexed with one line of code
 - Universal search index and retrieval mechanism
 - Works on shared hosts, no external service required
- Build in REST API
 - Every object can be exposed via RESTful API
 - Can be fine tuned via configuration file
- Web Security
 - CSRF (Cross Site Request Forgery) detection
 - Automatic value sanitising against SQL injection
 - Salted passwords
 - Models can store data using AES encryption in the database
- Database Migrations using Phinx
- Comprehensive Unit and Acceptance tests
- Install Wizard
- Open source GPL license
- Community development on github

## For Business Analysts / Power Users / Application Administrators

- Users and Contacts
- Groups of users
- Role based access control
 - Automatic per action
 - $user->hasRole() for fine grained control
 - Groups have roles, group members inherit those roles
 - Roles are functions
- Notifications and Inbox
 - Send internal notifications to users
 - Inbox with archiving
 - Send internal messages between users
 - Email notifications
- Reporting
 - Create reports in SQL
 - Make report available to group of users (or groups)
 - View report as html table, csv, pdf, or xml
 - Create feed url for report
- Mail Templates
 - Templates for emails, invoices, forms, etc.
 - Uses TWIG and accepts json data
- Wiki
    
    
    
# User Guide
## Introduction
Cmfive is a web application for phones and computers. 

Cmfive provides a number of modules that can be accessed through the menu.

Context sensitive help is available through the menu question mark.
## Admin
### Users
This module can be used to list all users, create, update and delete users.

They can also manage the role based access controls for this user using the Permissions button from the list. By checking roles, the user is allowed to use additional features.

Access to this module is limited to a user with `user` role.

### Groups
This module can be used to list all user groups, create, update and delete user groups.

After creating a group, a user can click the group title to edit the name or use the More Information button to update the roles based permissions that are associated with this group.

The More Information page also provides membership management to add and delete members from the group.

A user is considered to have access to all the roles in the user record and those of any groups the user is a member of.

Access to this module is limited to a user with `user` role.


### Permissions
Each module provides a number of roles as described below.

Each role is associated with behaviors of the module usually around limiting access. 
 
Roles are assigned as described above in Users and Groups.

    Admin
        comment
        user
    Favorites
        favorites_user
    File
        file_upload
        file_download
    Help
        help_view
        help_contact
    Inbox
        inbox_reader
        inbox_sender
    Report
        report_admin
        report_editor
        report_user
    Tag
        tag_admin
        tag_user
    Task
        task_admin
        task_user
        task_group
    Timelog
        timelog_user
    Example
        example_admin
        example_view
    Wiki
        wiki_user

### Lookup Tables
This module can be used to manage lookup items available to users. Lookup tables are used by other modules to provide options for radio, select and autocomplete fields. 

Access is limited to `admin users`.

### Templates
This module allows the management of template records.

Template records can be used in association with the reports module.

Access is limited to `admin users`.

### Printers
This module allows review and management of remote printers.

Access is limited to `admin users`.

### Print Queue
This module allows review and management of the remote print queue.

Access is limited to `admin users`.

### Backup
An admin user can use the backup action to dump the database to the webroot/backups folder.

Access is limited to `admin users`.

### Composer update
This action collates all the composer dependancies across all modules and then updates the available composer libraries.

Access is limited to `admin users`.

### Email
This module provides information about email in the system.???? I'm missing configuration to make this work ?

Access is limited to `admin users`.

### Migrations
The migration module allows an admin user to run database model updates resulting from changes to the software.

Migrations can be run forwards for update or backwards to revert model changes.

Access is limited to `admin users`.


## Tasks
### Working with Tasks
The tasks module allows management of users and tasks in groups.

To start, create a task group. It is then possible to create and assign tasks using the various submodules.

As a task assignee, the dashboard is a useful entry point to the tasks module.

### Dashboard
The Task Dashboard provides a summary of tasks and task groups relating to you. 

Summaries are shown for your tasks, overdue and urgent tasks.

Access to this module is limited to a user with `task` role.

### Task List
The Task List allows fine grained filtering of Tasks.

To view a Task in detail, click on the task title.

When searching for tasks, or when arriving here via the Task Dashboard, the filter will show the selections you made to arrive at the current list of Tasks. 

Selections available include:        
 
- **Assignee:** The person currently assigned to a task
- **Creator:** The person who created the task
- **Group:** The Task Group to which a task belongs
- **Type:** The type of task
- **Priority:** The task priority
- **Status:** The current task status
- **From/To Date:** Filter tasks by their due date occuring within a given date span
- **Include Closed Tasks:** Closed tasks are excluded by default, check this box to include closed task in your search returns.

Access to this module is limited to a user with `task` role.

### Taskgroups
This module allows managing groups and group members.

Create a new task group using the `New Task Group` button.

The task group requires a title. You must also assign values for who can create tasks in,view tasks in and assign users to tasks for this task group. 
The is active field allows temporarily disabling the task group.
There is also an optional description and defaults for task type, priority and assignee for tasks in this group.

You can modify a task and manage members and notifications by clicking on the title of the task group on the Manage Task Groups page. 
A detailed task view is shown including members and their roles. Membership and roles can be managed using the member list buttons.

The task group fields can be managed using the `Edit Task Group` button.

Notifications for this taskgroup can be managed using the `Notifications` tab.

You can also click `Task List` to see a list of tasks in this task group.

Access to this module is limited to a user with `task_group` role.

### Creating a Task
A form is provided for you to create new tasks.
By first selecting the Task Group, the form will allow you to make further selections relevant to that Task Group.
The form is a two-step process. Please complete all steps to properly register a Task.
The form consists of:
        Task Group: select the group most appropriate to the task you require fulfilled
        Task Title: enter a title outlining the task
        Task Type: select the type of task
        Priority: select your priority for the task
        Date Due: select the date on which you will need to task to be completed Description: enter an accurate and concise description of the task, providing as much information as is necessary
        Assigned: if suitably authorised, you may assign the task to a Group Member, otherwise you may only select 'Default'.
Click the 'Continue' button to continue the process
On the next form you will be asked further questions specific to the Group and Type of task as entered in the previous form. Complete this
form and click the 'Submit' button to submit the task.

Access to this module is limited to a user with `task` role.

### Task access controls
A task group has member users who can be GUESTS, MEMBERS AND OWNERS. They can also have ALL roles(what does this do????).

A task group has fields defining the abilities of each of GUESTS, MEMBERS AND OWNERS. 
- The visibility in search listings of tasks in this group is limited to members who match the `Who Can View` field.
- The ability to create new tasks in this group is limited to members who match the `Who Can Create` field.
- ????The ability to create assign users to tasks in this group is limited to members who match the `Who Can Assign` field.

Owners and members of a task group can be assigned to tasks in the task group.


### Task notifications

By default you will recieve all available notifications. You can limit this using this module to manage the notifications that you receive in response to task changes.

The module lists the Task Groups you are a member of, showing your role within that group and the types of notifications you will receive. 

You can set up each of your Task Groups so that you will be notified of events that take place to Tasks relevant to you. For any given group,you can click the `Edit` button to edit your notifications. 
Depending upon your role, you can receive notifications for Tasks you have created, for Tasks assigned to you or, if you are a Group Owner, for activity in any Task within that group.

You can also set the specific Task Events for which you wish to receive notifications. 

Each task group also has notification selections that affect the choices available in this module.

Access to this module is limited to a user with `task` role.


### Timelog 
The timelog modules provides a simple way to capture time logged against tasks. It lists of all time logs for the logged in user sorted by most recent. 

The timelog module also offers a quick entry for adding time logs. You must fill the module (Tasks), select a task from the autocomplete list and provide start and end dates and description.

If you visit a task page before clicking the add timelog button, the task will be automatically populated.

Access to this module is limited to a user with `task` role.

### Task Timer
When a editing a task, an extra element appears on the menu to start and stop a timer. 

When a timer is started you are asked to enter a description for the time log.

When a timer is stopped a new time log entry is created for this task.



## Report
### Working with reports
The reports module provides tools to develop and show summary reporting from a database system.

Report administrators can use a powerful combination of templating and sql queries to build reports.

The module provide fine grained access control by associating users with reports as described below under Report Members.

Users must have one of `report_admin`, `report_editor`, `report_user` roles to access this module.


### Report Dashboard
The reports dashboard lists reports that are available to the logged in user.

Where the user has permission, reports can be edited and deleted.

The report results can be accessed by clicking the report title in the list.


### Creating a Report
Start by clicking the `Create a Report` menu item. You only need to provide a title.
When you click `Save Report`, additional tabs are shown to enter more details about the report.

The SQL tab allows you to enter SQL queries that will be used in the report.

The Templates tab allows multiple templates to be associated with the report.
You must first create emplates to render the data. Create a System Template in Admin and set the module to the text `report` so it can be used for developing reports.

The template processer uses the Twig language, you can find more information about this on the Twig Website.

A good first step when creating a new template, is to look at the data. You can use the following twig statement in your template to do this:

{{dump(data)}}


### Report Members
Users can be associated with a report as OWNER, EDITOR or USER.

Users cannot access a report unless explictly associated in one of these roles.

All roles enable a user to list and view a reports results.

Only report administrators and EDITORs can edit and approve reports.

Only report adminstrators and OWNERs can delete reports.

### Report connections
This module stores Database connection parameters to be used in reports.

### Feeds dashboard
Reports can be exported as feeds. The feeds dashboard allows management of exported feeds.

## Inbox

This module provides features similar to an email in listing and filtering messages and allowing a user to create and reply to messages.

The system generates a number of notifications to individual users which can always be found here. Where a user has an email address set, notifications are sent to their email address as well.

Messages can be archived and deleted. This can be applied to many messages at once using the checkboxes.

Access to this module is limited to a user with `inbox_reader` role however only users with the `inbox_sender` role are allowed to send messages.


## Tag


## Wiki
The wiki module provides for collaborative document development using the popular markdown shorthand syntax for formatting.

Wikis can be created using the `New Wiki` menu item. You must provide a title. You may choose to make the wiki public, bypassing other access controls.

A detailed view of a wiki is available by clicking the title of the wiki in the list.

The Edit tab provides a textarea for editing the wiki content in Markdown syntax. 

A wiki contains many pages. By default, the wiki will open on it's home page. 

To create a new page, enter a link where you surround the page title with double square brackets. eg [[MyNewPage]] .
Page titles cannot include spaces.
Save the current wiki page and then click the link to the new page in the `View` tab to edit the new page.

The Wiki History and Page History tabs provide access to previous versions of the wiki pages.

Access to the wiki is managed using the Members tab. Users can be assigned or removed  to/from the wiki. 
Users are assigned as either `readers` or `editors` and `readers` will see a wiki with a restricted set of tabs.

Access to this module is limited to a user with `wiki_user` role.

Comments can be added to any wiki page.


## Forms

## Favorites
The favorites module allows tagging of certain types of records as favorites by clicking the `star icon` near the editing form. 

Records that have been tagged in this way are available as a list using the `star icon` in the menu.


## Top Menu Features
### Help
Context specific help is available for many pages in the system. 

Help can be accessed by clicking the question mark in the menu.

### Search
Through the menu search icon, it is is possible to search across many records in the system.

You must enter at least three letters to search. The single input text search typically searches across all text fields in records.

This search does not provide the fine grained selection of module based searches.

Search can be started with the keyboard shortcut Ctrl-F.

### Profile/Logout
The person icon in the top menu provides links to edit the logged in user profile and to logout.

### History Breadcrumbs.
The history panel shows recently visited pages.

## Channel and Processor workflow 
The channels module allows an `admin user` to configure how data from an external system is fed into the cmfive database.

For example email messages could be polled as a source, converted to tasks as a processor and result in notification messages to relevant users.

# Developer Guide

## Introduction
CmFive is a framework for developing web based business applications. 

The main focus is on facilitating fast development of functionality with minimal boilerplate code.

## Framework
A good place to learn about the framework is to look at the example module.
The tasks module provides more complex examples.

## Files and Directory Structure
A cmfive site includes the following top level files and directories
    config.php - the main configuration file for the site
    index.php - point of entry for all requests
    system - cmfive source code
    modules - module folders for this install are placed here
    cache - temporary items
    backup - storage location for dumps generated by the backup module
    log - log files are written here
    storage - provides a single folder to hold cache, backup, uploads and other files written by the web server
    uploads
    templates
    lib
    doc - generated api doc

A typical installation symlinks the cmfive repository system folder and copies other key top level files into the project folder. Other folders are created by the system on demand.

Inside the system folder are the following important files and directories
    composer
    modules
    classes
        AspectModifiable.php
        AspectSearchable.php
        AspectVersionable.php
        Config.php
        CSRF.php
        DbMigration.php
        DbObject.php
        DbPDO.php
        DbService.php
        DbTable.php
        History.php
        html
            a.php
            button.php
            form.php
            input.php
        SessionManager.php
    html.php
        html rendering functions
    functions.php
        miscellaneous functions
    web.php
        request handling and routing
    composer.json
        composer file for system dependancies
        composer dependancies are also configured in modules and the admin menu item for composer update handles unifying the dependancies.

## URL Routing

All HTTP request to a cmfive application are routed through index.php

The request url is used to determine which module and action should handle the request 
Url format is <domain>/<module>/<action>-<subaction>/<additional parameters> 
An action can choose to handle POST, GET or ALL requests by convention of function eg doSomething_POST()
  
## Structure of a Module

A module folder contains the following important files and directories
    
    config.php
        eg
        <?php 
        Config::set('example', array( 
         'active' => true, 
         'path' => 'modules', 
         'topmenu' => true, 
         'search' => array( 
                 "Example Data" => "ExampleData", 
         ), 
         'widgets' => array(), 
         'hooks' => array('core_dbobject','example'), 
         'processors' => array(), 
        ));
        options
            active
                the module can be deactivated by changing active value
            path
                path must be modules or system/modules
            topmenu
                should the module provide menu items
                    to customise menu items for this module, create a navigation function in the main service class.
            search
                label and database name to be used when searching records from this module
            widgets
            hooks
            processors

    models
        php model definition files
        service classes

    actions
        files in this folder can be routed to using an appropriate url
        where example/actions/edit.php contains a function called edit_ALL(), a request to the url http://domain/example/edit will include that php file and call the edit_ALL function

    templates
        when an action functions complete, output rendering is initiated using template files using a similar convention if one is available.
        for example the availability of /example/templates/edit.tpl.php (and example/actions/edit.php) will trigger first the action code and then the template code.
        The template code runs in a special context created by web for rendering.
        Data is passed from actions to templates using web context.
             ie $w->ctx("table","my table content"); means that the variable $table is available in the template.

    install
        this folder contains information required to build the database structure for this module
        historically this was sql files
        migrations using phynx is the new standard for defining a model.
            migration scripts are inside the subdirectory migrations
            migration scripts should not be modified once they are deployed, additional changes to a data structure should be defined and deployed as additional migration files.

    help
        files named <action>.help containing markdown content are automatically detected and deployed as context sensitive help.

    tests
        tests related to this module

    <module>.roles.php 
        defines the roles available in this module.
        the presence of a file called example.roles.php in the example module containing 
        function role_example_admin_allowed($w, $path) { return startsWith($path, "example"); }
        enable a role based access type for users and groups.
    <module>.hooks.php

    <module>.listeners.php 

## Models
        Models in Depth
        DbObject
        DbService
        Retrieving Many Objects
        Get One Object
        Create, Retrieve, Update, Delete
        Date and Time Transformations
        Making an Object Searchable
        Storing Object Modification Information
        Sanitising and Validation
        UI Hints
        Full Text Search
## Templates
        Templates in Depth
        Layout
        Html Helper Class
## Access Objects via Rest URIs
## Search Path
## Actions in Depth
	Redirection
	Context
## Modules
## Cache
## Actions
## Models
## Templates
## Help
## Listeners
## Roles
## Tasks
## Configuration Options
        Global $modules Array
        Changing System Module Configuration
        Overriding Another Module
## Authentication and Authorisation

## Database Migrations
        GUI in admin section - admin users only
        Batch migrations
            - all pending migrations can be run as a batch and that set of migrations is saved as the batch set so they can be rolled back as a batch
        - create a migration for a module wizard in GUI
            - edit generated migration file
            - up and down function as inverse
            - ensure hasTable, hasColumn before addtable etc
            - id field replacement for biginteger
            - phynx docs in composer (online is a version ahead)
            - MyDevMigration extends CmFiveMigration extends Phynx/AbstractMigration
                - dropTable - override to rename name
                - addCmFiveParameters - list fields you DONT WANT
        - discuss option to improve API on drop table

## Github workflow
        The new format for branching is as follows:
        master is more or less on par with latest production branch, but may include new features which are stable, but not yet released
        production branches are used for every major release, they will only accept bug fixes, but no new features can be added
            production branches should start with the name dev-
        feature branches are used for developing new features or fixing a particular bug, they will be merged into master
        bugfix branches are branched off of a production branch to develop and test a bugfix for that branch
        both feature and bugfix branches should be deleted after they have been merged

## Contributing
CmFive is an open source GPL licenced software framework and business web application. 

To contribute please send pull requests to https://github.com/2pisoftware/cmfive 

Please ensure that all tests pass before submitting patches. 

The cmfive core team user docker images to standardise and simplify development workflow. 

An image 2pisoftware/cmfive is available on the docker.io hub. 


## Test Framework
See [https://github.com/2pisoftware/testrunner/blob/master/README.txt](https://github.com/2pisoftware/testrunner/blob/master/README.txt)

CmFive uses codeception and phantom js for unit and acceptance tests. A related repository testrunner provides scripts for bootstrap and running of tests within CmFive

### Overview
What we have is

- a codeception based testing framework with phantomjs for headless testing and an scripted install process for cmfive from git.
- comprehensive unit tests for the web and db classes and config classes.
- acceptance tests for part of the admin module and the tasks module and the example module.
- a framework for acceptance testing that massively simplifies dealing with the javascript UI components we use in cmfive.
- still lots of work to do but already it's been useful in noticing bugs and changes.
- immediate future plans include finishing unit tests for the system classes (the worst is done) and moving into the service classes in parallel with acceptance tests across the rest of the core cmfive system and perhaps elements of crm on demand.
- a team policy that in so far as reasonable, all development tasks will include a testing component related to that feature for regression security. 
- We also have the start of a continuous integration deployment process using docker, jenkins and other tools.

It's hard to present visually because who wants to look at tests. We could use
- source code (I've attached a sample of test suite files)
- test run output
- screen shot output of acceptance tests (generated by a codeception extension)
- codeception web pages
### Testing Completeness
#### Core system classes
    Config
    Web
    DbService
    DbObject
#### Object Classes
    ./var/www/cmfive/modules/example/models:
        -ExampleData.php
    ./var/www/cmfive/modules/wiki/models:
        WikiException.php
        WikiExistsException.php
        WikiLib.php
        WikiNoAccessException.php
        WikiPageHistory.php
        WikiPage.php
        Wiki.php
        WikiUser.php
    ./var/www/cmfive/system/modules/admin/models:
        AdminMigration.php
        Audit.php
        CmfiveMigration.php
        CmfiveTable.php
        Comment.php
        GenericTransport.php
        Lookup.php
        MandrillTransport.php
        Migration.php
        Printer.php
        SwiftMailerTransport.php
        Template.php
    ./var/www/cmfive/system/modules/auth/models:
        Contact.php
        GroupUser.php
        User.php
        UserRole.php
    ./var/www/cmfive/system/modules/channels/models:
        ChannelMessage.php
        ChannelMessageStatus.php
        Channel.php
        ChannelProcessor.php
        EmailChannelOption.php
        EmailMessage.php
        EmailParser.php
        EmailStructure.php
        FileParser.php
        ProcessorType.php
        TestProcessor.php
        WebChannelOption.php
    ./var/www/cmfive/system/modules/favorite/models:
        Favorite.php
        favorites_widget.php
    ./var/www/cmfive/system/modules/file/models:
        Attachment.php
        AttachmentType.php
    ./var/www/cmfive/system/modules/help/models:
        HelpLib.php
    ./var/www/cmfive/system/modules/inbox/models:
        Inbox_message.php
        Inbox.php
        Sms.php
    ./var/www/cmfive/system/modules/main/models:
        WidgetConfig.php
    ./var/www/cmfive/system/modules/report/models:
        ReportConnection.php
        ReportFeed.php
        ReportLib.php
        ReportMember.php
        Report.php
        ReportTemplate.php
    ./var/www/cmfive/system/modules/rest/models:
        RestSession.php
    ./var/www/cmfive/system/modules/tag/models:
        Tag.php
    ./var/www/cmfive/system/modules/task/models:
        AComment.php
        GanttChart.php
        GanttLineEntry.php
        TaskComment.php
        TaskData.php
        TaskDependency.php
        TaskEmailPoll.php
        TaskGroupMember.php
        TaskGroupNotify.php
        TaskGroup.php
        TaskGroupType.php
        TaskGroupUserNotify.php
        TaskLib.php
        TaskObject.php
        Task.php
        TaskTime.php
        TaskType.php
        TaskUserNotify.php
    ./var/www/cmfive/system/modules/timelog/models:
        Timelog.php
####Service classes
    ./modules/example/models/ExampleService.php
    ./modules/wiki/models/WikiService.php
    ./system/classes/DbService.php
    ./system/modules/admin/models/AdminService.php
    ./system/modules/admin/models/AuditService.php
    ./system/modules/admin/models/CommentService.php
    ./system/modules/admin/models/LogService.php
    ./system/modules/admin/models/LookupService.php
    ./system/modules/admin/models/MailService.php
    ./system/modules/admin/models/MigrationService.php
    ./system/modules/admin/models/PrinterService.php
    ./system/modules/admin/models/TemplateService.php
    ./system/modules/auth/models/AuthService.php
    ./system/modules/channels/models/ChannelService.php
    ./system/modules/channels/models/ChannelsService.php
    ./system/modules/favorite/models/FavoriteService.php
    ./system/modules/file/models/FileService.php
    ./system/modules/inbox/models/InboxService.php
    ./system/modules/install/models/InstallService.php
    ./system/modules/main/models/MainService.php
    ./system/modules/main/models/WidgetService.php
    ./system/modules/report/models/ReportService.php
    ./system/modules/rest/models/RestService.php
    ./system/modules/search/models/SearchService.php
    ./system/modules/tag/models/TagService.php
    ./system/modules/task/models/TaskService.php
    ./system/modules/timelog/models/TimelogService.php
#### Action functions
#### Acceptance by features
    Features
        Admin
            Users
            Groups
            Permissions
                Admin
                    comment
                    user
                Favorites
                    favorites_user
                File
                    file_upload
                    file_download
                Help
                    help_view
                    help_contact
                Inbox
                    inbox_reader
                    inbox_sender
                Report
                    report_admin
                    report_editor
                    report_user
                Tag
                    tag_admin
                    tag_user
                Task
                    task_admin
                    task_user
                    task_group
                Timelog
                    timelog_user
                Example
                    example_admin
                    example_view
                Wiki
                    wiki_user
            Lookup Tables
            Templates
            Printers
            Print Queue
            Backup
            Composer update
            Email
            Migrations
        Tasks
            Dashboard
            Task List
            Task access controls
            Creating a Taskgroup
            Creating a Task
            Working with Tasks
            Task notifications
            Timelog
        Report
            Report Dashboard
            Access a Report
            Creating a Report
            Report connections
            Feeds dashboard
        Inbox
        Tag
        Wiki
        Forms
        Favorites
        Help
        Search
        Profile/Logout
        History
                
#### Access Controls
Each modules provides a number of roles that change the behavior of the UI. Most fundamentally, only members with appropriate roles will be able to access features in a module.

- We need acceptance tests to ensure that any actions limited by roles are truly restricted.
- We need acceptance tests to ensure that any allowed actions can be success stories.

Access controls by module are listed above.
