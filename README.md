[![Build Status](https://app.travis-ci.com/Gizra/drupal-starter.svg?branch=main)](https://app.travis-ci.com/Gizra/drupal-starter)

# Drupal 9 Starter

Starter repo for Drupal 9 development. This starter is an opinionated approach,
with the following concepts and tools:

1. [ddev](https://ddev.readthedocs.io/) should be the only requirement, and
every operation should happen inside ddev's containers. For example, one should
not ever need to execute commands such as `composer install` from the host
machine. Instead we have `ddev composer install`. The advantage is that we have
a consistent, reproducible and shareable environment, so developers don't have
to lose time over configuration of their host machine.
1. [Robo](https://robo.li/) is the task manager, and is favored over Bash
scripts. The reason for this is that it's
assumed PHP developers are more comfortable with PHP than Bash, and it provides
us with easier iteration, reading and manipulating yaml files, pre-defined
[tasks](https://robo.li/tasks/Assets/), etc.
1. We use Travis-CI for continuous integration. A pre-configured and working
`.travis.yaml` is part of this repo.
1. We use Pantheon for hosting. A `ddev robo deploy:pantheon` will take care of
deployments. See more under ["Deploy to Pantheon"](#deploy-to-pantheon) section.
1. We use [Pluggable Entity View Builder](https://www.drupal.org/project/pluggable_entity_view_builder) to define how an entity should look like. See [example](https://github.com/Gizra/drupal-starter/blob/main/web/modules/custom/server_general/src/Plugin/EntityViewBuilder/NodeLandingPage.php).

## Requirements

* [DDEV](https://ddev.readthedocs.io/en/stable/)

## Installation

    ddev composer install
    cp .ddev/config.local.yaml.example .ddev/config.local.yaml
    ddev restart

Once the Drupal installation is complete you can use `ddev login` to
log in to the site as user 1 using your default browser.

## Theme Development

By default, `ddev restart` compiles the theme using Robo (`ddev robo theme:compile-debug`)

This is used only for watching Tailwind styles, it's not compiling js, images, etc.

On the local development environment, which is using TailWind's [JIT](https://tailwindcss.com/docs/just-in-time-mode) (Just-In-Time), execute:

```bash
ddev theme:watch
```

This will compile Tailwind and keep watching for any changes.

When running `ddev robo theme:compile` it will purge any TailWind's CSS class
which is not found in the code, twig, or under `tailwind.config.js` `whitelist` property.

The directory structure:
 - `src/` - put all source stylesheets images, fonts, etc here.
 - `dist/` - `.gitignore`-ed path where the compiled / optimized files live, the theme should refer the assets from that directory.

For theme development, it's advisable to entirely turn off caching: https://www.drupal.org/node/2598914

### Breakpoints and Responsive Images

It is advised to use Drupal's Responsive image module.

If there are new breakpoints added, or existing breakpoints updated in
`server_theme/tailwind.config.js`, you must ensure to also update the Drupal
breakpoints configuration file for the theme `server_theme.breakpoints.yml` so
that the media queries for the responsive images are in sync with tailwind's.
It is advisable to finalize this configuration before any responsive image
styles get added, otherwise you will need to ensure the existing responsive
image styles are also re-configured for the new/updated breakpoints.

Currently, the breakpoints are configured to follow [Tailwind's breakpoints](https://tailwindcss.com/docs/responsive-design)
for example `sm`, `md`, etc.

### How to get dimensions for, and configure the responsive image styles

This process should be done as a last step of a "wiring" of the frontend
component with Drupal. This is because we rely on the frontend component for our
dimensions. These are guidelines to usually follow, but not set in stone, it's
always a bit of a judgement call:

1. Figure out the rules.

   Each frontend component which outputs a user-uploaded content image is unique
design-wise, so you should study the design closely or contact the designer for
any clarifications of how an image should transform at various widths. For
example whether the image height is static (i.e. always `400px`), or whether
there is a max width for the full width hero image.
2. Figure out the biggest dimensions needed for each breakpoint.

   Now that we know the rules, we can figure out the biggest dimension image
needed for each of our breakpoints. You should always start with mobile. Go to
the styleguide page (or any output of the component) and set your browser to the
highest dimension of the breakpoint. For example on Mobile it's `639px`, because
at 640px the `sm` breakpoint starts, for `md` it's `1023px` as the `lg`
breakpoint starts at `1024px`. Now just check the image dimension output and
note the width and height.
3. Create the image styles for each breakpoint.

   Now that we have all the needed information, we can create the image styles.
For naming we use this style, but you're free to figure out your own method as
long as it's consistent:
    - Label: `[Component name] [breakpoint] [multiplier] ([width]x[height)`

      The width and height isn't necessary, we simply add it for aesthetics.
    - Machine name: `[component_name]_[breakpoint]_[multiplier]`
    - Example:
      - `Hero md 1x (900x600)` - `hero_md_1x` (Scale and Crop)
      - `Content image md 2x (1800w)` - `content_image_md_2x` (Scale only)

   Note: For the 2x multiplier, simply double the dimensions.
4. Create the responsive image style.

   Use the `server_theme`'s breakpoints when creating the responsive image style
and assign the image styles created in step3 to each breakpoint.
5. Finally use the responsive image style in the wire-up of the component with
Drupal. With PEVB, see `BuildFieldTrait::buildMediaResponsiveImage()`.

## ElasticSearch

The starter kit comes out of the box with ElasticSearch. Search API is activated and DDEV provides an ElasticSearch instance, already configured to use a [stopwords](https://github.com/Gizra/drupal-starter/blob/master/config/elasticsearch/stopwords.txt) and a [synonyms](https://github.com/Gizra/drupal-starter/blob/master/config/elasticsearch/synonyms.txt) list. Also it creates 4 indices (QA, DEV, TEST and LIVE) to reflect our typical Pantheon setup. The site inside DDEV will use the DEV index.
To take a look, you can check these first:
 - https://drupal-starter.ddev.site:9201/
 - https://drupal-starter.ddev.site:9201/\_cat/indices - list of all indices
 - https://drupal-starter.ddev.site:9201/\_search - list of all documents

## PHPCS (Code Sniffer)

    ddev phpcs

## Tests

For testing we use [Drupal Test Traits](https://medium.com/massgovdigital/introducing-drupal-test-traits-9fe09e84384c) (DTT), as it allows a very fast and convinent way of testing existing installation profiles.
See the [example](https://github.com/Gizra/drupal8-starter/blob/master/web/modules/custom/server_general/tests/src/ExistingSite/ServerGeneralExampleTest.php) test.

    # Run all tests
    ddev phpunit

    # Run a single test file
    ddev phpunit --filter ServerGeneralHomepageTest

    # Run a single method from a test file.
    ddev phpunit --filter testHomepageCache web/modules/custom/server_general/tests/src/ExistingSite/ServerGeneralHomepageTest.php

## Deploy to Pantheon

### Pantheon Setup

Follow the steps listed in `.ddev/providers/pantheon.yaml`.
Make sure to add the correct site name under `environment_variables.project`.

There's a Robo command to do the entire process of creating a new project:
```
ddev robo bootstrap:project newname git@github.com/newclient/newproject.git terminus_token github_token
```
See the details [here](https://github.com/Gizra/drupal-starter/blob/main/robo-components/BootstrapTrait.php).

As this repository gets copied several times, for different projects, it gets tedious to port small fixes.
For larger-scale changes, due to conflicts and per-project considerations, we need to apply
changes manually., However for tiny, trivial changes, such as Travis fixes, we have the following tool:
```
# Go to the root of all the projects
cd /home/user/your-projects
# If no export is provided, command will ask entering manually the names.
export REPOSITORIES=[client1 client2 client3]
/path/to/starter/scripts/mass_patch.sh [gh_token_that_can_create_prs] /tmp/our-little-patch "PR title"
```

This must be executed natively (i.e. no inside `ddev ssh`).
It will try to refresh the working copies there and apply the patch. If it succeeds, it opens
a pull request in the destination repository.
That provides a fast track to spread changes for those parts of the Starter Kit that typically
remain unchanged after cloning (CI scripts, testing scripts, DDEV configuration and commands, and so on).

You can also specify the repositories using a configuration file:
```
cp scripts/mass_patch.example.config.sh scripts/mass_patch.config.sh
```
Then edit `scripts/mass_patch.config.sh` and add the proper project names.

#### Create your site

Then, you can create a new site in Pantheon which can also be done with a
[terminus command](https://pantheon.io/docs/guides/drupal8-commandline):

    ddev exec terminus site:create my-site "My Site" "Drupal 9"

#### Change to nested docroot structure

To allow Pantheon to work with composer managed sites and recognize the `web`
directory, we need to follow the [Pantheon instructions](https://pantheon.io/docs/nested-docroot#disable-one-click-updates)

When following the instructions, clone the pantheon repository in the required
location with this command:

    git clone ssh://codeserver.dev.<long-hash>.drush.in:2222/~/repository.git .pantheon

In order to successfully install and import configuration you will need to add the
config directory in the `web/sites/default/settings.php` file:

    $settings['config_sync_directory'] = dirname(DRUPAL_ROOT) . '/config/sync';

### Executing

In case you haven't done so before, make the DDEV container aware of your ssh.

    ddev auth ssh

### Install the Site with the Profile

After first deploy, you will want to install the site:
`ddev robo deploy:pantheon-install-env dev`

This command is also useful if a deployment got stuck due to non-deployable
config changes, so it can reboot the environment from scratch.

### Pantheon's `settings.php`

During the deployments, `web/sites/default/settings.pantheon.php` gets copied
to `web/sites/default/settings.php` into the Pantheon repository, so any kind
of configuration override (SMTP credentials, dev mode alterations) can be
injected to that file.

## Deploy Environments

To Deploy to a Pantheon environment (e.g. TEST or LIVE) you can use

    # With no argument, deploys to TEST.
    ddev robo deploy:pantheon-sync

    # Deploy to LIVE.
    ddev robo deploy:pantheon-sync live

### Release notes

Deployments should imply a release, you can generate a release notes based on
tags.
In order to provide verbose release notes, it is required to [create a personal
access token](https://docs.github.com/en/github/authenticating-to-github/keeping-your-account-and-data-secure/creating-a-personal-access-token).
At the token [creation page](https://github.com/settings/tokens/new), grant `repo` scope (all permissions) to the new token.

To have the token for all projects in one step, you can edit the global DDEV configuration file:
```bash
ddev config global --web-environment-add="GITHUB_USERNAME=your_github_username"
ddev config global --web-environment-add="GITHUB_ACCESS_TOKEN=your_github_access_token"
```

Then you can generate a changelog using

    ddev robo generate:release-notes

Or you can specify a tag that's the base of the comparison.

    ddev robo generate:release-notes 0.1.2

One line in the changelog reflects one merged pull requests, and the command
assembles it from the Git log.

## Automatic Deployment to Pantheon

In order to deploy upon every merge automatically by Travis, you shall:

1. Initiate QA (`qa` branch) multidev environment for the given project.
1. Double-check if `./.ddev/providers/pantheon.yaml` contains the proper Pantheon project name.
1. Get a [Pantheon machine token](https://pantheon.io/docs/machine-tokens) (using a dummy new Pantheon user ideally, one user per project for the sake of security)
1. Get a GitHub Personal access token, it is needed for [Travis CLI to authenticate](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token). It will be used like this: `travis login --pro --github-token=`. Also it will be used to post a comment to GitHub to the relevant issue when a merged PR is deployed, so set the expiry date far in the future enough for this.
1. `ddev robo deploy:config-autodeploy [your terminus token] [your github token]`
1. `git commit -m "Deployment secrets and configuration"`
1. Add the public key in `travis-key.pub` to the newly created dummy [Pantheon user](https://pantheon.io/docs/ssh-keys)
1. Actualize `public static string $githubProject = 'Gizra/the-client';` in the `RoboFile.php`.

Optionally you can specify which target branch you'd like to push on Pantheon, by default it's `master`, so the target is the DEV environment, but alternatively you can issue:
`ddev robo deploy:config-autodeploy [your terminus token] [your github token] [pantheon project name] [gh_branch] [pantheon_branch]`

After you have automatic deployment for a project, you are able to deploy to Pantheon `test` and `live` using Git tags.
`git tag 0.1.2` will imply a deployment to the `test` environment (and `dev` - as enforced by Pantheon).
`git tag 0.1.2_live` will imply a deployment to `live`. In order to make it fast, you need to first create the tag that deploy to `test`, then you need to tag the same commit with a tag suffixed with `_live`.

## Pulling DB & Files From Pantheon

    ddev auth ssh

    # Pull DB & Files
    ddev pull pantheon

## Stage File Proxy
If you don't want to copy production files locally, you can enable stage_file_proxy module.
It saves you time and disk space by sending requests to your development environment's files directory
to the production environment and making a copy of the production file in your development site.

Configure the origin path at `/admin/config/system/stage_file_proxy`.

## Migrate

There are existing migrations that help setup a typical site, and act as an
example. Whenever working on the migration, and changing their configuration
you will need to re-sync the config, and re-run the migrations.

    ddev drush en server_migrate -y
    ddev drush config-import --partial --source=modules/custom/server_migrate/config/install/ -y
    ddev drush migrate:rollback --all
    ddev drush migrate:import --group server
    # Set the homepage.
    ddev drush set-homepage

## Flood Control

As the project uses Redis, it is not possible to use the SQL console to reset flood table.
There's a custom DDEV command to help with that. Usages:

```bash
ddev pantheon-flood-flush
ddev ddev-flood-flush
```

Purges all the entries from Pantehon's `live` environment or DDEV's own Redis.

```bash
ddev pantheon-flood-flush test
```

Purges all the entries from Pantheon's `test` environment.

```bash
ddev pantheon-flood-flush test 193.165.2.3
ddev ddev-flood-flush 193.165.2.3
```

Purges entries related to IP `193.165.2.3` from Pantheon's `test` environment, or alternatively from DDEV's own Redis.


## Importing/Exporting translations

There are 2 types of translations that we manage in this site by code. These are:

- UI translations
- Config translations

### UI translations

UI translations are strings that pass through `TranslatableMarkup` basically. They are defined mostly in twig files and
in PHP classes. The UI translations files are in `config/po_files` directory:

- `ar.po`
- `es.po`

#### Exporting UI translations

When new translatable strings are added, a dev should:

- Enable potx module `ddev drush en potx`
- Export the translation strings from the file. For example, if you added a new string in server_general.module:
  - `drush potx --files modules/server_general/server_general.module`
  - Open `web/general.pot` file, copy the new translatable string you added and paste it into each language po file.
- Provide the updated po file to the translators.

Once these are translated by translators and provided back to devs, devs will need to simply commit the changes.

#### Importing UI translations

These files are imported automatically on deploys to Pantheon. And on ddev if you have `exec: robo locale:import` in
your ddev's `config.local.yaml` they will be imported on ddev start.

To run the import manually on ddev: `ddev robo locale:import`.

### Config translations

Config translations are for translating config entities, such as a Node Type. The Config translations files are in
`config/po_files` directory and the file names end with `_config`:

- `ar_config.po`
- `es_config.po`

#### Exporting Config translations

When new modules are installed, or new configuration is added to the site, a dev should re-export the config
translations and provide it to a translator for updating.

First you must identify the strings which will need to be added to the list of translatable config strings.
To do this simply update the `managed-config.txt` file and add the config name (without the `.yml`) followed by a colon
and the config key that you want to translate. For example, to add "News" node type's bundle label to the list,
simply add `node.type.news:name` to the file in a new line.

Then you need to run `ddev robo locale:export-from-config` which will update the config po files.

#### Importing Config translations

These files are __*not*__ imported automatically. When a dev receives updated `*_config.po` file, they need to manually
import the po file.

To import the config translations:

- Run `ddev robo locale:import-to-config`
- Run `ddev drush config:export`
- Review & commit the config changes

## Two-factor Authentication (TFA)

TFA is enabled for the Administrator and Content editor users.
The default settings under `/admin/config/people/tfa` define "Skip Validation" is 1. That is,
when a privileged user will login, they must enable their TFA. Otherwise, on a second
login, they will already be blocked. A site admin may reset their validation tries
under the `/admin/people` page.
The TFA method that is enabled is one that uses Google authenticator (or similar).

