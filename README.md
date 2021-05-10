# Coding Challenge - Lumen
This challenge covers **all of the provided tiers (ie. 1, 2, and 3)** and is created using the following stacks:
**Laravel / Lumen**, **MySQL** for the database, and **Docker** containers that serve as the infrastructure.

# Requirements
It is recommended that the application is run using docker as it will spawn the necessary setups without the need to do so manually.
Otherwise, the .env and .env.testing files were committed into the repository for the sake of showing how things were setup locally.

# Instruction (using docker)

## Running the code
1. Clone the code base locally by running the following git command
`git clone git@github.com:luckmansugiarto/cengage.git <project name>`

2. Open your terminal / command prompt (in windows), navigate to the project's root directory, and run the following docker command
`docker-compose up -d`. This docker command will instantiate 3 separate containers: app, main mysql database, and testing mysql database. The initial bootup process will take quite a fair bit of time as docker will run **composer install**.

3. Login to the **app** container by typing in this command `docker exec -it app sh`

4. Inside the container, go to the root directory of the project that is located in **./app**

5. run database migration using `php artisan migrate:fresh --seed` command from the root directory of the project or run this command for unit testing: `./vendor/bin/phpunit`

The main app is available on **http://localhost:8080** URL and the following are list of available API endpoints along with their corresponding HTTP verbs:

* **DELETE** /session/_{session ID}_ => Delete an existing session based on the given _{session ID}_
* **DELETE** /session/_{session ID}_/book/_{book ID}_ => Remove an existing book as identified by _{book ID}_ from an existing session identified by _{session ID}_
* **GET** /auth/token_details => Retrieve authentication token information
* **GET** /sessions => Retrieve a list of available sessions for the authenticated user
* **GET** /session/_{session ID}_ => Get details of an existing session identified by _{session ID}_
* **POST** /session => Create a new session for current authenticated user. valid POST attributes are:
    * name = session's name
    * end_time = session's end date time (valid format: **yyyy-mm-dd hh:ii:ss**)
    * start_time = session's start date time (valid format: **yyyy-mm-dd hh:ii:ss**)
* **PUT** /session/_{session ID}_/book/_{book ID}_ => Assign a book identified by _{book ID}_ to an existing session identified by _{session ID}_
