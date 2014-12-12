#!/bin/bash

cd $(dirname $0)/..

find . -name "CMakeCache.txt" -exec rm -f {} \;
find . -name "cmake_install.cmake" -exec rm -f {} \;
find . -name "CMakeFiles" -exec rm -fr {} \;
find . -name "Makefile" -exec rm -fr {} \;
rm -rf install_manifest.txt

echo "Cmake Cache cleared!"


