#!/bin/bash
# Goto D8 folder
cd ../
# Save all log entries in text file.
exec >> $1.txt 2>&1
cd $1/docroot
pwd