FROM ctf-sshd:latest

## speed
RUN useradd -m -d /home/vador -s /bin/bash vador
RUN echo 'vador:dark' | chpasswd
COPY --chown=vador speed/speed /home/vador/speed
RUN chmod 500 /home/vador/speed
COPY --chown=root speed/.vimrc /home/vador/.vimrc
COPY --chown=root speed/.bash_logout /home/vador/.bash_logout
RUN mkdir /home/vador/tmp
RUN chown vador:vador /home/vador/tmp
RUN touch /home/vador/toto
RUN chmod 666 /home/vador/toto
RUN chown root:root /home/vador
RUN apt-get update && apt-get install -y vim

