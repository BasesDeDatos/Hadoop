#!/bin/bash
hdfs dfs -rm -r /user/hduser/outputdata
cd
cd Escritorio/mapreducef
javac -classpath /usr/local/hadoop/share/hadoop/common/hadoop-common-2.7.1.jar:/usr/local/hadoop/share/hadoop/mapreduce/hadoop-mapreduce-client-core-2.7.1.jar:/usr/local/hadoop/share/hadoop/common/lib/commons-cli-1.2.jar -d /home/hduser/Escritorio/mapreducef *.java

#Meter archivos .class a mapreducec
mv *.class mapreducec
jar -cvf mapreducej.jar -C /home/hduser/Escritorio/mapreducef/mapreducec .

#Correr
cd
cd $HADOOP_INSTALL
bin/hadoop jar /home/hduser/Escritorio/mapreducef/mapreducej.jar MapReduce /user/inputdata outputdata
hdfs dfs -cat /user/hduser/outputdata/part-r-00000
hdfs dfs -cat /user/hduser/outputdata/part-r-00001
#hdfs dfs -cat /user/hduser/outputdata/part-r-00002
