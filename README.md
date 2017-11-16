
# Privalia Code Challenge 2017

The 2017 event of the **Privalia Code Challenge** is called **Maze Escape**! The goal is easy: You have to create a bot to escape from a labyrinth.

Your bot will be trapped in a maze and is expected to find its way out competing with the other player bots in a real time challenge. All the bots will start at the same random position and they won't be visible to each other. The first bot to find its way out of the maze wins the game.

## Configuration

This repo constains the server used for the **Privalia Code Challenge 2017**.
It's a [PHP](http://php.net/) project which uses the [Symfony 3](https://symfony.com/) framework and a [MySQL](https://www.mysql.com/) database.
It has been developed using **PHP7.1** and **MySQL5.7** and [docker](https://www.docker.com/) technologies.
The `docker` folder constains the particular images used in the development environment and some [bash](https://www.gnu.org/software/bash/) commands with helps configurating the environment.

- `docker/build.sh` - Build the docker images.
- `docker/start.sh` - Start all the project containers.
- `docker/stop.sh` - Stop all the project containers.
- `docker/composer.sh` - Execute composer inside the container (params are allowed). The containers must be started before run this script.
- `docker/console.sh` - Execute a console command inside the container (params are allowed). The containers must be started before run this script.
- `docker/phpunit.sh` - Execute phpunit unit tests inside the container (params are allowed). The containers must be started before run this script.
- `docker/bash.sh` - Access bash shell of the API container. The containers must be started before run this script.
- `docker/su.sh` - Access bash shell of the API container with the `root`user. The containers must be started before run this script.

NOTE: All these scripts asume there is an user `david` in the host. You can change it for your usere name to avoid  permission problems.

### Installation

```
$ git clone git@github.com:PrivaliaTech/code-challenge-2017.git
$ cd code-challenge-2017
$ docker/build.sh
$ docker/start.sh
$ docker/composer.sh install
$ docker/console.sh doctrine:database:create
$ docker/console.sh doctrine:schema:create
```

## Documentation

### Maze generation

- [Wikipedia: Maze generation algorithm](https://en.wikipedia.org/wiki/Maze_generation_algorithm)
- [Maze Generation: Recursive Division](http://weblog.jamisbuck.org/2011/1/12/maze-generation-recursive-division-algorithm)
- [Stack Overflow: What's a good algorithm to generate a maze?](http://stackoverflow.com/questions/38502/whats-a-good-algorithm-to-generate-a-maze)

## Credits

### Icons 
Icons from [Flaticon](http://www.flaticon.com) with [Creative Commons BY 3.0 License](http://creativecommons.org/licenses/by/3.0/):

- [Labyrinth icon](http://www.flaticon.com/free-icon/labyrinth_182580) was made by [Zlatko Najdenovski](http://www.flaticon.com/authors/zlatko-najdenovski)
- [Pacman icon](http://www.flaticon.com/free-icon/pacman_131412) was made by [Baianat](http://www.flaticon.com/authors/baianat)
- [Ghost icon](http://www.flaticon.com/free-icon/ghost_387112) was made by [Freepik](http://www.flaticon.com/authors/freepik)
- [Trophy icon](http://www.flaticon.com/free-icon/trophy_321773) was made by [Freepik](http://www.flaticon.com/authors/freepik)
- [Mansory icon](http://www.flaticon.com/free-icon/mansory_351764) was made by [Freepik](http://www.flaticon.com/authors/freepik)
- [Tombstone icon](http://www.flaticon.com/free-icon/tombstone-with-cross_78204) was made by [Freepik](http://www.flaticon.com/authors/freepik)
- [Racing flag icon](http://www.flaticon.com/free-icon/racing-flag_65578) was made by [Vectors Market](http://www.flaticon.com/authors/vectors-market)
- [Map marker icon](http://www.flaticon.com/free-icon/map-marker-point_34369) was made by [Simpleicon](http://www.flaticon.com/authors/simpleicon)
- [Creative Commons icon](http://www.flaticon.com/free-icon/creative-commons-circular-logo_78110) was made by [Plainicon](http://www.flaticon.com/authors/plainicon)
