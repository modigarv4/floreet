#!/bin/bash

# Only run this if you're an authorized dev
if [ ! -d "config" ]; then
  echo "Cloning private config repository into config/"
  git clone git@github.com:modigarv4/floreet-config.git config
else
  echo "Config directory already exists."
fi
