package hadooptodb;

import java.io.File;
import java.io.FileNotFoundException;
import java.sql.*;
import java.util.ArrayList;
import java.util.Scanner;

public class HadoopToDB {
    
    // JDBC driver name and database URL
    static final String JDBC_DRIVER = "com.mysql.jdbc.Driver";  
    static final String DB_URL = "jdbc:mysql://localhost/test";

    //  Database credentials
    static final String USER = "prueba";
    static final String PASS = "prueba";
    
    static Connection conn = null;
    
    public static void connectToDB() throws ClassNotFoundException, SQLException{
        Class.forName("com.mysql.jdbc.Driver");
        conn = DriverManager.getConnection(DB_URL,USER,PASS);
    }
    
    public static void main(String[] args) throws SQLException, ClassNotFoundException, FileNotFoundException {
        connectToDB();
        Statement stmt = conn.createStatement();
        String sql;
        String[] address;
        String[] line;
        ArrayList<String> row = new ArrayList();
        /*
        Scanner partTotal = new Scanner(new File("partTotal.txt"));
        System.out.println("Mapeando tabla totalCount");
        while(partTotal.hasNextLine()){
            line = partTotal.nextLine().toString().split("\t");
            sql = "INSERT INTO totalCount(word, count) VALUES ('" + line[0] + "', '" + line[1] + "')";
            stmt.execute(sql);
        }
        
        Scanner partPage = new Scanner(new File("partPage.txt"));
        System.out.println("Mapeando tabla pageCount");
        while(partPage.hasNextLine()){
            line = partPage.nextLine().toString().split("\t");
            address = line[0].split("<##>");
            sql = "INSERT INTO pageCount(count, word, address, title) VALUES ('" + line[1] + "', '" + address[0] + "', '" + address[1] + "', '" + address[2] + "')";
            stmt.execute(sql);
            row.clear();
        }
        */
        Scanner partList = new Scanner(new File("partList.txt"));
        System.out.println("Mapeando tabla addressXword");
        while(partList.hasNextLine()){
            String[] newAddress;
            line = partList.nextLine().split("\t");
            sql = "INSERT INTO word(word) VALUES ('" + line[0] + "')";
            stmt.execute(sql);
            address = line[1].split("<!!>");
            for (String add : address){
                newAddress = add.split("<##>");
                sql = "INSERT INTO addressXword(word_id, address, title) VALUES ( LAST_INSERT_ID(), '" + newAddress[0] + "', '" + newAddress[1] + "')";
                stmt.execute(sql);
            }
        }
        /*
        Scanner input = new Scanner(new File("pruebaExportar.txt"));
        
        String[] map;
        
        while(input.hasNextLine()){
            map = input.nextLine().toString().split("<##>");
            System.out.println(map[0] + "\t" + map[1] + "\t" + map[2]);
        }
        */
    }
    
}
