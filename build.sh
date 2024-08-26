#!/bin/bash
image_name="asco-server"

# Build Docker container
docker build -t $image_name /server

# Run Docker Compose
docker-compose up