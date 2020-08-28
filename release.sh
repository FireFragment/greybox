#!/usr/bin/env bash

# Use like ./release.sh v1.0.0

# ------------------- BASIC RELEASE INITIALIZATION
# Add tag latest commit
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



# ------------------- ASK FOR RELEASE DETAILS
# Get title input
echo What title should the release have?
read title

# Get changelist input
printf "Write changelist for this release. Each point on a new line, prefixed by * sign. End with CTRL + D.\n"
readarray -t changelist

# Load API token from config file
GIT_API_TOKEN=$(<.GIT_TOKEN)

echo "Creating release..."



# ------------------- CREATE RELEASE
# POST tag name, release name and changelog to GitHub API
releaseResult=$(curl \
   -X POST \
   -H "Accept: application/vnd.github.v3+json" \
   -H "Authorization: token ${GIT_API_TOKEN}" \
   https://api.github.com/repos/loudislav/greybox/releases \
   -d "{\"tag_name\": \"${1}\", \"name\": \"${title}\", \"body\": \"${changelist}\"}" | tr "\n" " " )

echo "Release created, uploading files..."

# Replace occurences inside script to prevent JSON parse fail
releaseResult=${releaseResult//\\r\\n/"-"}

# Parse request JSON response and get upload URL
releaseUploadUrl=$(python3 -c "import json; print(json.loads('${releaseResult}')['upload_url'])")

# Remove unnecessary appendix in URL
releaseUploadUrl=${releaseUploadUrl%\{?name,label\}}



# ------------------- UPLOAD BUILD FILES
# Choose which files with which labels to upload
fileNames=("registrace.zip", "registrace_debugbox.zip")
fileLabels=("Production%20build", "Development%20build")

# Loop through files to upload
for i in ${!fileNames[@]};
do
    fileName=${fileNames[$i]}
    fileLabel=${fileLabels[$i]}
    fileSize=$(stat -c%s ./${fileName})

    # Do the acutal upload
    curl \
        -v \
        -X POST \
        -H "Accept: application/vnd.github.v3+json" \
        -H "Authorization: token ${GIT_API_TOKEN}" \
        -H "Content-Length: ${fileSize}" \
        -H "Content-Type: application/octet-stream" \
        --data-binary @"${fileName}" \
        ${releaseUploadUrl}?name=${fileName}\&label=${fileLabel}

    echo "File ${fileName} uploaded..."
done
