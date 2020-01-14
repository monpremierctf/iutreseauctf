FROM ubuntu:16.04

RUN apt-get update && apt-get install -y openssh-server 
RUN mkdir /var/run/sshd
#RUN echo 'root:Sicacestpasunxfrgtyh34drdebongrospassword' | chpasswd
#RUN sed -i 's/PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config

# SSH login fix. Otherwise user is kicked off after login
RUN sed 's@session\s*required\s*pam_loginuid.so@session optional pam_loginuid.so@g' -i /etc/pam.d/sshd

ENV NOTVISIBLE "in users profile"
RUN echo "export VISIBLE=now" >> /etc/profile


EXPOSE 22
CMD ["/usr/sbin/sshd", "-D"]


RUN useradd -m -d /home/totor -s /bin/bash totor
RUN echo 'totor:lerotor' | chpasswd
RUN apt-get update
RUN apt-get install -y nano
RUN apt-get install -y openjdk-8-jdk
RUN apt-get install -y unzip

COPY read.txt /home/totor/

COPY dossiers.zip /home/totor/
RUN unzip -d /home/totor/ /home/totor/dossiers.zip
RUN rm /home/totor/dossiers.zip
RUN chown -R totor:totor /home/totor/Dossier1
