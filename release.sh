#!/usr/bin/env bash

# Use like ./release.sh v1.0.0

# Tag commit
git tag $1

# Push commit tag
git push origin $1

# Remove old builds
rm -rf registrace registrace_debugbox
rm -f registrace.zip registrace_debugbox.zip

# Build production & debug
npm run build
npm run build:debug

# ZIP created files
zip -r registrace.zip registrace
zip -r registrace_debugbox.zip registrace_debugbox

# Get title input
echo What title should the release have?
read title

# Get changelist input
printf "What changes are in this release? Each point on a new line started by *, end with CTRL + D\n"
readarray -t changelist

# TODO - upload ${title}, ${changelist} and created files to GitHub https://developer.github.com/v3/repos/releases/#create-a-release