#!/usr/bin/env bash

# Use like ./release.sh

# ------------------- ASK FOR RELEASE DETAILS
# Get release version
echo "What version is this release (vX.Y.Z)"
read version

# Get title input
echo "What title should the release have?"
read title

# Get changelist input
printf "Write changelist for this release. Each point on a new line, prefixed by * sign. End with CTRL + D.\n"
readarray -t changelist

# Merge changelist array with line breaks
printf -v changelist '\\n%s' "${changelist[@]}"

# Remove the leading line break
changelist=${changelist:2}



# ------------------- PUSH TAG TO REPOSITORY
# Add tag latest commit
git tag ${version}

# Push commit tag
git push origin ${version}



# ------------------- DELETE OLD BUILDS AND CREATE NEW ONES
# Remove old builds
rm -rf registrace registrace_debugbox registration
rm -f registrace.zip registrace_debugbox.zip registration.zip

# Build production & debug
npm run build
npm run build:debug
npm run build:int

# Create zip files
if ! command -v tar &> /dev/null
then
    # tar command not found -> Windows
    tar.exe -a -c -f registrace.zip registrace
    tar.exe -a -c -f registrace_debugbox.zip registrace_debugbox
    tar.exe -a -c -f registration.zip registration
else
    # tar command found -> Linux
    tar -a -c -f registrace.zip registrace
    tar -a -c -f registrace_debugbox.zip registrace_debugbox
    tar -a -c -f registration.zip registration
fi



# ------------------- CREATE RELEASE
printf "\nCreating release...\n"

# Load API token from config file
GIT_API_TOKEN=$(<.GIT_TOKEN)

# POST tag name, release name and changelog to GitHub API
releaseResult=$(curl \
    -X POST \
    -H "Accept: application/vnd.github.v3+json" \
    -H "Authorization: token ${GIT_API_TOKEN}" \
    https://api.github.com/repos/loudislav/greybox/releases \
    -d "{\"tag_name\": \"${version}\", \"name\": \"${title}\", \"body\": \"${changelist}\"}" | tr "\n" " " )

# Replace occurences of new lines inside script to prevent JSON parse fail
releaseResult=${releaseResult//\\r\\n/"-"}
releaseResult=${releaseResult//\\n/"-"}

# Parse request JSON response and get upload URL
if ! command -v py &> /dev/null
then
    # py command not found -> Linux
    releaseUploadUrl=$(python3 -c "import json; print(json.loads('${releaseResult}')['upload_url'])")
else
    # py command found -> Windows
    releaseUploadUrl=$(py -c "import json; print(json.loads('${releaseResult}')['upload_url'])")
fi

# Remove unnecessary appendix in URL
releaseUploadUrl=${releaseUploadUrl%\{?name,label\}}



# ------------------- UPLOAD BUILD FILES
printf "\nRelease created, uploading files...\n"

# Choose which files with which labels to upload
fileNames=("registrace_debugbox.zip" "registrace.zip" "registration.zip")
fileLabels=("Development%20build" "Production%20build" "PDS%20production%20build")

# Loop through files to upload
for i in ${!fileNames[@]}; do
    sleep 1

    fileName=${fileNames[$i]}
    fileLabel=${fileLabels[$i]}
    fileSize=$(stat -c%s ./${fileName})

    # Do the acutal upload
    curl \
        --silent --output /dev/null \
        -v \
        -X POST \
        -H "Accept: application/vnd.github.v3+json" \
        -H "Authorization: token ${GIT_API_TOKEN}" \
        -H "Content-Length: ${fileSize}" \
        -H "Content-Type: application/octet-stream" \
        --data-binary @"${fileName}" \
        ${releaseUploadUrl}?name=${fileName}\&label=${fileLabel} \
        2>/dev/null

    printf "\nFile ${fileName} uploaded...\n"

    sleep 1
done
