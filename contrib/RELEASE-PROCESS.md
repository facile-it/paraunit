# Paraunit release process

## Disclaimer
This is the checklist that should be followed for the creation of a new Paraunit release, including the PHAR creation.

This is here for pratical usage, but also to leave a trace and to make this available to everyone that would like to do the same with their project. For the PHAR creation, I take for granted that the commands are launched inside the Docker container that is distributed with this project, so there are a few prerequisite in place:
 * Composer installed
 * Box2 installed
 * PHP zip extension enabled (for PHAR compression)
 * PHP ini value: `phar.readonly=0`
 * A GPG key (for signing)

## Process

### Tag
 * Check build status: it has to be green on both [Travis](https://travis-ci.org/facile-it/paraunit) and [Appveyor](https://ci.appveyor.com/project/Jean85/paraunit)
 * Check that the changelog is up to date
   * Tag paragraph is present
   * Release date and version is correct in title
   * All changes and fixes are reported
 * Check that the release version is correct 
 * Tag the commit (with GPG sign, `-s`) and push
```
git tag -s -a 0.x -m "Release 0.x"
git push --follow-tags
```

### PHAR

#### Prepare the vendors

 * Checkout the desired tagged version
```
git checkout 0.7.3
```
 * Require the lowest PHP version supported:
```
composer config platform.php 5.3.3

```
 * Update/downgrade the vendors and remove dev dependencies:
```
composer update --no-dev
```
 
#### Generate the PHAR
 * Generate the PHAR using Box:
```
box build -v
```
 * Test the generated PHAR (not with Paraunit's testsuite itself, otherwise the autoloader will clash with itself)
 * Check the PHAR size to avoid bloating it (0.7.3 is around 1Mb) and update Box's exclusion list if necessary
```
ls -l paraunit.phar
```

#### Generate the signature
 * Generate the GPG sign
```
gpg --detach-sign --output gpg/paraunit-0.7.3.phar.asc paraunit.phar
```
 * Verify the PHAR signature
```
gpg --verify gpg/paraunit-0.7.3.phar.asc paraunit.phar
```

#### Restore the dev environment
 * Restore `composer.json`
```
git checkout composer.json
```
 * Restore the vendor folder
```
composer update
```

### Release on GitHub
 * Create a new release on the [GitHub release page](https://github.com/facile-it/paraunit/releases)
  * Select the tag created and pushed in the first step
  * Copy-paste the changelog section for this release
  * Upload the PHAR and its signature

### Release notice on the site
 * Checkout the GitHub Pages branch
```
git checkout gh-pages-source
```
 * Create a new release news on in `content/release`
  * Name the file as the release version
  * Add (if necessary) some text regarding the release at the top
  * Copy-paste the changelog section for this release
 * Deploy to HitHub pages
```
./deploy.sh
```
 * Commit changes to GitHub
 * Tweet about it!
