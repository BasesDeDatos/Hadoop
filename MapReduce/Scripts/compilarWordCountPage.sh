#!/bin/bash
hdfs dfs -rm -r /user/hduser/outputdataPage
cd
cd Escritorio/wordcountPagef
javac -classpath /usr/local/hadoop/share/hadoop/common/hadoop-common-2.7.1.jar:/usr/local/hadoop/share/hadoop/mapreduce/hadoop-mapreduce-client-core-2.7.1.jar:/usr/local/hadoop/share/hadoop/common/lib/commons-cli-1.2.jar -d /home/hduser/Escritorio/wordcountPagef *.java

#Meter archivos .class a wordcountc
mv *.class wordcountc
jar -cvf wordcountj.jar -C /home/hduser/Escritorio/wordcountPagef/wordcountc .

#Correr
cd
cd $HADOOP_INSTALL
bin/hadoop jar /home/hduser/Escritorio/wordcountPagef/wordcountj.jar WordCountPage /user/inputdata outputdataPage
hdfs dfs -cat /user/hduser/outputdataPage/part-r-00000
