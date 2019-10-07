<h1 align="center"> Gamification-Quiz </h1>

***

## General stuff
- Online Quiz using gamification and achievements within the scope of my bachelor thesis

- The main point was to investigate the different impacts of achievements on the users of a quiz.

There are three groups. The user gets randomly assigned to one of them:
1. Control Group: no gamification at all 
2. Normal Achievement Group: the user is able to get at most 53 achievements
3. Hyper Achievements Group: the user is able to get at most 151 achievements

- The quiz has 330 questions, divided into  16 categories with 20 questions and 1 category with 10 questions.

***

## How to run the quiz on localhost:

1) Download and install [XAMPP](https://www.apachefriends.org)

- For windows it is the best to install XAMPP directly in the `C:` folder
- Standard install should be working, you need to run `Apache` and `Mysql` modules from XAMPP control panel (Apache on ports 80,443 and Mysql on 3306)

2) Open phpmyadmin and configure the database

- Make sure the mysql server is up and running
- Open in browser with `localhost/phpmyadmin`
- On the left, create a new database called `quiz_flavio`
- Go to import and import the `quiz_flavio.sql` file from the folder before this `Quiz_source` folder. Now you should have three tables within your Databse `quiz_flavio`

3) Put the quiz source into the XAMPP directory

- Find the `htdocs` folder in the XAMPP installation path (on windows for example `C:\xampp\htdocs`)
- Paste all the files from this folder right in there
- Now you should be able to run the website on your localhost (make sure Apache Server is up and running)
- If you make database-related entries, they should be sent do the tables of your local database

4) Everything should be up and running. If not, contact me via e-mail: <flavioschroeder@gmx.de>