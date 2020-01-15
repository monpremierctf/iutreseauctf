#include <stdio.h>
#include <string.h>

int main (int argc, char * argv[])
{
short flag[50];
for(int i=0;i<strlen(argv[1]);i++)flag[i]=(short)argv[1][i];
for(int i=0;i<strlen(argv[1]);i++)printf("%d,",flag[i]);
}
