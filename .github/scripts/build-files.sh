#!/bin/bash
# Inspired by Elementor Releasing Pipeline https://github.com/elementor/elementor
set -eo pipefail

echo "Remove Old Dist - Start"
rm -rf ./admin/dist
rm -rf ./public/build
echo "Remove Old Dist - End"

echo "Composer Install"
composer install
echo "Yarn Install"
yarn
echo "Build"
yarn build
