# Sports API

[![Build Status](https://travis-ci.com/Eredost/Sports-api.svg?branch=main)](https://travis-ci.com/Eredost/Sports-api)
[![Maintainability](https://api.codeclimate.com/v1/badges/c99ade3f41a4ee59895d/maintainability)](https://codeclimate.com/github/Eredost/Sports-api/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/c99ade3f41a4ee59895d/test_coverage)](https://codeclimate.com/github/Eredost/Sports-api/test_coverage)

Rest API developed with Symfony 4.4 and PHP 8 providing sports data and
compliant with level 1 and 2 of the Richardson maturity model.

## Setup

Before you can set up the project, you will need the Docker Compose tool
installed on your machine.

1. Clone the repository
2. Build and run the containers:

   ```shell
   docker-compose up -d
   ```

3. Open a terminal on the 'symfony' container:

   ```shell
   docker-compose exec symfony bash
   ```

4. From the bash terminal, install the dependencies:

   ```shell
   composer install
   ```

5. Finally, populates the database and generates rsa keypair:

   ```shell
   # Create the tables in the database
   php bin/console doctrine:migrations:migrate
   
   # Generate the fake data set
   php bin/console doctrine:fixtures:load
   
   # Generate keypair using OpenSSL
   php bin/console lexik:jwt:generate-keypair
   ```

When the 'symfony' container is launched, **port 8080** is listening
and allows you to access the API.

HTML documentation is available at the route '/api/doc'.
