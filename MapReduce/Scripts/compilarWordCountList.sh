#!/bin/bash
hdfs dfs -rm -r /user/hduser/outputdataList
cd
cd Escritorio/wordcountListf
javac -classpath /usr/local/hadoop/share/hadoop/common/hadoop-common-2.7.1.jar:/usr/local/hadoop/share/hadoop/mapreduce/hadoop-mapreduce-client-core-2.7.1.jar:/usr/local/hadoop/share/hadoop/common/lib/commons-cli-1.2.jar -d /home/hduser/Escritorio/wordcountListf *.java

#Meter archivos .class a wordcountc
mv *.class wordcountc
jar -cvf wordcountj.jar -C /home/hduser/Escritorio/wordcountListf/wordcountc .

#Correr
cd
cd $HADOOP_INSTALL
bin/hadoop jar /home/hduser/Escritorio/wordcountListf/wordcountj.jar WordCountList /user/inputdata outputdataList
hdfs dfs -cat /user/hduser/outputdataList/part-r-00000
