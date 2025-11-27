QUIZ QUEST – AZURE DEPLOYMENT PROJECT

Quiz Quest is a simple quiz website built to explore and practice real-world cloud deployment workflows.
While the quiz itself is straightforward, the main focus of this project is on deploying a 2-tier web application using Azure App Service, Docker, and GitHub Actions.


🚀 PROJECT GOALS

🔹Learn and apply cloud deployment practices using Microsoft Azure

🔹Deploy a 2-tier architecture (Frontend + MySQL Database)

🔹Containerize the application using Docker

🔹Implement CI/CD automation with GitHub Actions

🔹Troubleshoot multi-language components (PHP, HTML, CSS, JS)

🔹Understand end-to-end workflow: local development → Docker → Azure



🏗️ ARCHITECTURE OVERVIEW

 User
 
   ↓
   
 Azure App Service (Docker Container)
 
   ↓
   
 MySQL Database (Azure / External)
 


🧰 TECH STACK

🔹Frontend: PHP, HTML, CSS, JavaScript

🔹Backend / Logic: PHP

🔹Database: MySQL

🔹Containerization: Dockerfile

🔹Deployment: Azure App Service

🔹Automation: GitHub Actions



⚙️ KEY FEATURES

🔹User registration & login

🔹Quiz system (answer & submit)

🔹Score tracking

🔹Minimal UI focused on functionality

🔹Fully containerized deployment

🔹Automated build + deployment pipeline



📦 DEPLOYMENT PROCESS

🔹Build Docker image

🔹Push image to GitHub Container Registry / Docker Hub

🔹Azure App Service pulls & runs the container

🔹Environment variables configured for database

🔹CI/CD pipeline automates build + deployment on new commits

🔹A full walkthrough of the deployment process is included in commit history & workflows.



📝 NOTES

This project is not focused on complex UI/UX or advanced quiz features.

Its primary purpose is to practice:

🔹Cloud deployment

🔹Containerization

🔹Automation

🔹Debugging multi-language components

🔹Understanding a full end-to-end workflow

