docker system prune 
docker system prune -a
docker images purge
docker rm $(docker ps -a -f status=exited -q)
docker stop $(docker ps -a -q)
docker rm $(docker ps -a -q)
docker volume prune



