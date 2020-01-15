#include <stdio.h>
#include <time.h>
#include <string.h>
#include <stdlib.h>


/* Fonction d'obtention d'un timestamp précis à la seconde */
int getheure()
{
struct tm *heure;
time_t intps;
intps=time(NULL);
heure = localtime(&intps);
return(heure->tm_hour*3600 + heure->tm_min*60 + heure->tm_sec);
}

/* Fonction permettant d'afficher le flag */
void printflag(char * flag)
{
 printf("BRAVO ! Le flag est: %s\n",flag);
}

/* prog principal */
int main (int argv, char * argc[])
{

int laps,avant,apres;
char texte[6][30]= {"1","10","riri","fifi","rififi","courage ceci est la dernière"};
char rep[30];
char flag[50]={102,108,97,103,123,99,104,52,109,112,49,48,110,45,100,117,45,109,48,110,100,51,33,125};

for (int i=0;i<6;i++) {
	printf("\nChaîne à saisir : %s \n",texte[i]);
	avant=getheure();	
	fgets(rep,sizeof(rep),stdin);
	apres=getheure();
	if(strlen(rep)==1 || strncmp(texte[i],rep,strlen(rep)-1)){printf("Raté ! Recommencez !\n");exit(0);}
	if((apres-avant)>1){printf("Trop lent ! Recommencez !\n");exit(0);}

	}

printflag(flag);

}
