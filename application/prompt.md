The previous/old dashboard has been kept/restored, which is NOT what I want.

I want you to work with the **currently existing dashboard** in this CodeIgniter project and modify that dashboard according to the application's existing functions, modules, roles, and permission system.

## Important Instructions

First inspect the current project carefully.

Identify:

* The current dashboard controller
* Current dashboard view
* Current sidebar/menu/navigation
* Existing modules and functions
* Existing routes
* Existing Role Setting → Permission functionality
* Existing user roles/rules/permissions
* Existing permission-checking functions/helpers/libraries
* Existing dashboard statistics/widgets/cards/links
* How menu items currently map to controllers and functions

### Do NOT

* Do not bring back the previous/old dashboard.
* Do not create a second dashboard.
* Do not replace the current dashboard with a completely new design.
* Do not create duplicate menu/module functionality.
* Do not hard-code permissions or menu access if equivalent functionality already exists.
* Do not remove working existing dashboard functions.

## Required Change

Use the **current dashboard as the base**.

Modify it according to the functions/modules that already exist in the project.

The dashboard, sidebar, menus, submenus, cards, shortcuts, and actions should reflect the actual existing application functionality.

Integrate the Role/Permission system with this current dashboard.

For example:

* If a user has permission for a module, show the relevant menu/dashboard option.
* If the user does not have permission, hide that menu/option.
* If a parent menu contains submenus, show only the permitted submenus.
* If no submenu under a parent is permitted, hide the parent menu as well.
* Dashboard cards/shortcuts must also respect permissions.
* Super Admin should continue to have access to all available functionality.

Most importantly, permission enforcement must also happen on the backend. Hiding dashboard/menu items alone is not sufficient. Users must not be able to access unauthorized controller methods by entering URLs manually.

## Preserve Existing Functionality

Before changing the dashboard, inspect all existing functions and determine which dashboard/menu items correspond to them.

Then modify the current dashboard using those existing functions.

Keep the existing:

* UI/theme
* layout
* controllers
* models
* routes
* module structure
* working dashboard features

as much as possible.

Only change what is necessary to properly integrate the Role/Permission system.

## Fix Previous Changes If Necessary

Review the changes previously made for the Role/Permission implementation.

If those changes accidentally restored the old dashboard, created duplicate dashboard code, replaced current functionality, or introduced duplicate menu definitions, correct those changes.

Remove only the duplicate/incorrect implementation created by the previous changes. Do NOT delete legitimate legacy code or existing application functionality without first checking its usage.

## Expected Result

After the modification:

1. The application must use the **current existing dashboard**, not the previous/old dashboard.
2. The current dashboard design should remain recognizable.
3. Existing application functions/modules should continue working.
4. Dashboard menus should be generated/filtered according to existing functions and assigned permissions.
5. Role Setting → Permission should control appropriate dashboard access.
6. Super Admin should see all available modules/functions.
7. Normal users should see only permitted modules/functions.
8. Direct unauthorized URL access must be blocked by backend permission checks.
9. There should be no duplicate dashboard, duplicate menu system, or duplicate permission system.

Before writing code, inspect the current implementation and understand which dashboard is actually being used. Then make the necessary changes directly in that implementation.

After completing the work, report:

* Which dashboard files were identified as the currently active dashboard
* Which old/duplicate dashboard code was causing the issue
* Which files you modified
* What dashboard/menu changes you made
* How existing functions were mapped to permissions
* How menu visibility is now controlled
* How backend authorization is enforced
* Whether any previous incorrect changes were removed or corrected

Do not just explain what needs to be done. **Inspect the project and implement the changes.**
