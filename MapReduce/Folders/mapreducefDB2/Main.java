import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.mapreduce.Job;
import org.apache.hadoop.mapreduce.lib.db.DBConfiguration;
import org.apache.hadoop.mapreduce.lib.db.DBInputFormat;
import org.apache.hadoop.mapreduce.lib.db.DBOutputFormat;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.io.IntWritable;
import org.apache.hadoop.io.NullWritable;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.input.TextInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;
import org.apache.hadoop.mapreduce.lib.output.TextOutputFormat;
import org.apache.hadoop.fs.Path;

public class Main
{
   public static void main(String[] args) throws Exception
   {
     Configuration conf = new Configuration();
     DBConfiguration.configureDB(conf,
     "com.mysql.jdbc.Driver",   // driver class
     "jdbc:mysql://localhost:3306/test", // db url
     "root",    // user name
     "root"); //password

     Job job = new Job(conf);
     job.setJarByClass(Main.class);
     job.setMapperClass(Map.class);
     job.setReducerClass(Reduce.class);
     job.setMapOutputKeyClass(Text.class);
     job.setMapOutputValueClass(Text.class);
     job.setOutputKeyClass(DBOutputWritable.class);
     job.setOutputValueClass(NullWritable.class);
     job.setInputFormatClass(TextInputFormat.class);
     job.setOutputFormatClass(DBOutputFormat.class);

     FileInputFormat.addInputPath(job, new Path (args[0]));

     DBOutputFormat.setOutput(
     job,
     "totalCount",    // output table name
     new String[] { "word", "count" }   //table columns
     );

     System.exit(job.waitForCompletion(true) ? 0 : 1);
   }
}
