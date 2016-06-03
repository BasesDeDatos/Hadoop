import java.io.IOException;
import java.io.DataOutput;
import java.io.DataOutput;
import java.util.*;
import java.sql.ResultSet;
import java.sql.PreparedStatement;
import java.sql.SQLException;

import org.apache.hadoop.fs.Path;
import org.apache.hadoop.conf.*;
import org.apache.hadoop.io.*;
import org.apache.hadoop.mapreduce.*;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.input.TextInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;
import org.apache.hadoop.mapreduce.lib.output.TextOutputFormat;
import org.apache.hadoop.mapreduce.lib.db.DBWritable;
import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.mapreduce.Job;
import org.apache.hadoop.mapreduce.lib.db.DBConfiguration;
import org.apache.hadoop.mapreduce.lib.db.DBInputFormat;
import org.apache.hadoop.mapreduce.lib.db.DBOutputFormat;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.io.IntWritable;
import org.apache.hadoop.io.NullWritable;

public class MapReduce{
        
    public static class Map extends Mapper<LongWritable, Text, Text, Text>{
	
		public void map(LongWritable key, Text value, Context context) throws IOException, InterruptedException{
				String[] line = value.toString().split("<##>");
				String palabra = line[0];
				String direccion = line[1] + "<##>" + line[1];
				context.write(new Text(palabra), new Text(direccion));
		}
    }
        
    public static class Reduce extends Reducer<Text, Text, DBOutputWritable, NullWritable>{
        
        public void reduce(Text key, Iterable<Text> values, Context context) throws IOException, InterruptedException{
        
            int sum = 0;
            for (Text val : values){
                sum++;
            }
            String total = Integer.toString(sum);
            //context.write(key, new Text(total));
            
			try {
				context.write(new DBOutputWritable(key.toString(), sum), NullWritable.get());
			} 
			catch(IOException e) {
				e.printStackTrace();
			}
			catch(InterruptedException e) {
				e.printStackTrace();
			}
        /*
			String direcciones = "";
			for (Text val : values){
				direcciones += val.toString();
			}
			context.write(key, new Text(direcciones));
		
		
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
		*/
        }
        
        
    }
    
    public static void main(String[] args) throws Exception{
        //Configuration conf = new Configuration();
        
        Configuration conf = new Configuration();
		DBConfiguration.configureDB(conf,
		"com.mysql.jdbc.Driver",   // driver class
		"jdbc:mysql://localhost:3306/test", // db url
		"root",    // user name
		"root"); //password
			
        Job job = new Job(conf, "MapReducejob");
        job.setJarByClass(MapReduce.class);
        
        job.setOutputKeyClass(Text.class);
        job.setOutputValueClass(Text.class);
        
        job.setMapperClass(Map.class);
        job.setReducerClass(Reduce.class);
        
        job.setMapOutputKeyClass(Text.class);
		job.setMapOutputValueClass(Text.class);
		
		job.setOutputKeyClass(DBOutputWritable.class);
		job.setOutputValueClass(NullWritable.class);
        
        job.setInputFormatClass(TextInputFormat.class);
        job.setOutputFormatClass(DBOutputFormat.class);
        
        FileInputFormat.addInputPath(job, new Path (args[0]));
        //FileOutputFormat.setOutputPath(job, new Path (args[1]));
        
        DBOutputFormat.setOutput(
		job,
		"totalCount",    // output table name
		new String[] { "word", "count" }
		);
        
        job.waitForCompletion(true);
    }
    
}
