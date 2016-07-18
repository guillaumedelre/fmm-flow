# fmm-flow

fmm-flow Phar application to behave like git-flow.

See the [git-flow](http://danielkummer.github.io/git-flow-cheatsheet/) website for more informations.

## Install

    composer install

Install the project by running the command `composer install`. 
Then Run the build.

## Build

    ./box.phar build

- Rename file `box.json.dist` to `box.json`. 
- run `box.phar build` to build a PHAR file. 
- launch the app `./build/fmm-flow.phar`.

See the [box](http://box-project.github.io/box2/) tool for more informations.

## Usage

    ./build/fmm-flow

For more informations on each command, use --help.

Available main commands are :
-  fmm-flow init
-  fmm-flow feature start myFeature
-  fmm-flow feature finish myFeature
-  fmm-flow feature publish myFeature
-  fmm-flow feature retrieve myFeature
-  fmm-flow release start <x.x.x>
-  fmm-flow release finish <x.x.x>
-  fmm-flow release publish <x.x.x>
-  fmm-flow release retrieve <x.x.x>
-  fmm-flow self-update
-  fmm-flow bump <x.x.x>

## Update

    ./build/fmm-flow self-update

## Bump

    ./build/fmm-flow bump 1.2.1

- create a tag and push master. 
- update & push manifest on branch gh-pages. 

See the [github project page](https://help.github.com/articles/creating-project-pages-manually) for more informations.

## Todo

Integrate git subplit in the workflow ?