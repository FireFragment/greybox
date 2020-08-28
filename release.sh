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

# Load API token from config file
GIT_API_TOKEN=$(<.GIT_TOKEN)

echo "Creating release..."

# POST tag name, release name and changelog to GitHub API
curl \
    -X POST \
    -H "Accept: application/vnd.github.v3+json" \
    -H "Authorization: token ${GIT_API_TOKEN}" \
    https://api.github.com/repos/loudislav/greybox/releases \
    -d "{\"tag_name\": \"${1}\", \"name\": \"${title}\", \"body\": \"${changelist}\"}"

# TODO - uploade assets to GitHub release https://developer.github.com/v3/repos/releases/#create-a-release