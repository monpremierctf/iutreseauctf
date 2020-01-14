FROM ubuntu:16.04

RUN apt-get update && apt-get install -y openssh-server
RUN mkdir /var/run/sshd
RUN echo 'root:Sicacestpasunxfrgtyh34drdebongrospassword' | chpasswd
RUN sed -i 's/PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config

# SSH login fix. Otherwise user is kicked off after login
RUN sed 's@session\s*required\s*pam_loginuid.so@session optional pam_loginuid.so@g' -i /etc/pam.d/sshd

ENV NOTVISIBLE "in users profile"
RUN echo "export VISIBLE=now" >> /etc/profile


EXPOSE 22
CMD ["/usr/sbin/sshd", "-D"]


RUN useradd -m -d /home/engm1 -s /bin/bash engm1
RUN echo 'engm1:enigme1' | chpasswd
RUN apt-get update
RUN apt-get install -y nano
RUN apt-get install -y openjdk-8-jdk
RUN mkdir /home/engm1/enigme
RUN chown engm1:engm1 /home/engm1/enigme
COPY enigme.class flag.txt notice.txt resultat.txt /home/engm1/enigme/
RUN chown -R engm1:engm1 /home/engm1/enigme
