import java.lang.Math;
import java.io.*;
import java.util.Scanner;

public class enigme {
    public static void main(String[] args) throws InterruptedException, IOException {
        double max=50000;
        double min=40000;
        double enigme = (int)(Math.random()*((max-min)+1))+min;
        String enigme_result = String.format("%.0f", enigme);
        double enigme_d_print = enigme*7;
        String enigme_print = String.format("%.0f", enigme_d_print);
        System.out.println(enigme_print);
        String run = "go";
        String resultat_recup;
        int compt=0;
        while (run.equals("go")) {
            if (compt!=10) {
                resultat_recup = recup_texte();
                if (resultat_recup.equals(enigme_result)) {
		    String flag="fgzef ef zefv zevb fzei fze fu4f 5sd435f4 sd4f53 4sd35f453 sd\n";
                    BufferedWriter writer = new BufferedWriter(
                            new FileWriter("/home/engm1/enigme/flag.txt", true)  //Set true for append mode
                    );
                    //writer.newLine();   //Add new line
                    writer.write(flag);
                    writer.close();
                    run = "stop";
                }
                Thread.sleep(200);
                compt += 1;
            }
            else {
                run = "stop";
            }
            }
        }

    public static String recup_texte() throws FileNotFoundException {
        File file = new File("/home/engm1/enigme/resultat.txt");
        Scanner sc = new Scanner(file);
        String data = sc.nextLine();
        return data;
    }
}
