#!/bin/bash
hdfs dfs -rm -r /user/hduser/outputdataTotal
cd
cd Escritorio/wordcountTotalf
javac -classpath /usr/local/hadoop/share/hadoop/common/hadoop-common-2.7.1.jar:/usr/local/hadoop/share/hadoop/mapreduce/hadoop-mapreduce-client-core-2.7.1.jar:/usr/local/hadoop/share/hadoop/common/lib/commons-cli-1.2.jar -d /home/hduser/Escritorio/wordcountTotalf *.java

#Meter archivos .class a wordcountc
mv *.class wordcountc
jar -cvf wordcountj.jar -C /home/hduser/Escritorio/wordcountTotalf/wordcountc .

#Correr
cd
cd $HADOOP_INSTALL
bin/hadoop jar /home/hduser/Escritorio/wordcountTotalf/wordcountj.jar WordCountTotal /user/inputdata outputdataTotal
hdfs dfs -cat /user/hduser/outputdataTotal/part-r-00000
