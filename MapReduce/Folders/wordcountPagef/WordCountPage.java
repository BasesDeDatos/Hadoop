import java.io.IOException;
import java.util.*;

import org.apache.hadoop.fs.Path;
import org.apache.hadoop.conf.*;
import org.apache.hadoop.io.*;
import org.apache.hadoop.mapreduce.*;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.input.TextInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;
import org.apache.hadoop.mapreduce.lib.output.TextOutputFormat;

public class WordCountPage{
    
    public static class Map extends Mapper<LongWritable, Text, Text, Text>{
	
		public void map(LongWritable key, Text value, Context context) throws IOException, InterruptedException{
				String[] line = value.toString().split("<##>");
				String palabra = line[0];
				String direccion = line[1];
				context.write(new Text(palabra), new Text(direccion));
		}
    }
    
    public static class Reduce extends Reducer<Text, Text, Text, Text>{
        
        public void reduce(Text key, Iterable<Text> values, Context context) throws IOException, InterruptedException{
            			String occurrences;
			String newKey;
			
			List<String> elements = new ArrayList<String>();
			for (Text val : values) {
				elements.add(val.toString());
			}
			
			List<String> uniqueElements = new ArrayList<String>();
			for (String element : elements){
				if (!uniqueElements.contains(element)){
					uniqueElements.add(element);
				}
			}
						
			for(String element : uniqueElements){
				occurrences = Integer.toString(Collections.frequency(elements, element));
				newKey = key.toString() + "<##>" + element;
				context.write(new Text(newKey), new Text(occurrences));
			}
        }
        
    }
    
    public static void main(String[] args) throws Exception{
        Configuration conf = new Configuration();
        
        Job job = new Job(conf, "WordCountPagejob");
        job.setJarByClass(WordCountPage.class);
        
        job.setOutputKeyClass(Text.class);
        job.setOutputValueClass(Text.class);
        
        job.setMapperClass(Map.class);
        job.setReducerClass(Reduce.class);
        
        job.setInputFormatClass(TextInputFormat.class);
        job.setOutputFormatClass(TextOutputFormat.class);
        
        FileInputFormat.addInputPath(job, new Path (args[0]));
        FileOutputFormat.setOutputPath(job, new Path (args[1]));
        
        job.waitForCompletion(true);
    }
    
}