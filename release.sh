#!/bin/bash

# Set the script to exit immediately if any command exits with a non-zero status
set -e

# Save the current directory in a variable
current_directory=$(pwd)

# Extract the folder name from the current directory
folder_name=$(basename "$current_directory")

# Define temporary folder using the extracted folder name
temp_folder="/tmp/$folder_name"

# Check if Composer is installed
command -v composer >/dev/null 2>&1 || { echo >&2 "Composer is required but not installed. Aborting."; exit 1; }

# Create a temporary folder
mkdir -p "$temp_folder" || { echo "Failed to create a temporary folder"; exit 1; }

# Copy all files and subfolders to the temporary folder
cp -r . "$temp_folder"

# Extract version from PHP file
version=$(grep -o "Version:[[:space:]]*[0-9.]*" "$temp_folder/$folder_name.php" | sed 's/Version:[[:space:]]*//')


# Change the working directory to the temporary folder
cd "$temp_folder"

# Run composer install without dev dependencies
composer install || { echo "Composer install failed"; exit 1; }


# List of files and folders to delete
unwanted_files=(
    .dockerignore
    .env
    .gitignore
    .gitmodules
    .phpcs.xml.dist
    .travis.yml
    bitbucket-pipelines.yml
    docker-compose.yml
    Dockerfile
    phpunit.xml.dist
    .git/
    wiki/
    releases/
    docker/
    toolset-config/
    bin/
    dev/
    logs/
    app/Config/phinx-local.php
    *.zip
    yakpro-po.cnf
    release.sh   # Change the script filename
    Thumbs.db    # Remove Windows thumbnail cache file
    .DS_Store    # Remove macOS default file
)

# Loop through and delete unwanted files and folders
for item in "${unwanted_files[@]}"; do
    # shellcheck disable=SC2115
    rm -rf "$temp_folder/$item"
done

# Delete any .gitkeep files
find "$temp_folder" -name '.gitkeep' -delete

# Create releases folder if it doesn't exist
mkdir -p "$current_directory/releases"

cd "/tmp"
# Create a zip file from the cleared temp folder with specific compression settings
zip -r -X "$current_directory/releases/$folder_name.$version.zip" "$folder_name"/*

# Clean up the temporary folder
cd "$current_directory"  # Return to the original directory
rm -rf "$temp_folder"
