---
name: deploy
description: Uploads the current project code to the main branch on git so it syncs with the server.
---

# Deploy Skill

When the user invokes the `/deploy` command or asks to deploy the project to the server:

1. Use `git status` to check if there are pending changes.
2. If there are changes, use `git add .` and `git commit -m "Deploy to main server"`.
3. Save the current branch name using `git branch --show-current`.
4. Switch to the main branch: `git checkout main`.
5. Merge the previous branch into main: `git merge <previous_branch>`.
6. Push the changes to the server: `git push origin main`.
7. Switch back to the previous working branch so the user can continue developing: `git checkout <previous_branch>`.
8. Notify the user that the stable version has been successfully pushed to the main branch and synced with the server.
