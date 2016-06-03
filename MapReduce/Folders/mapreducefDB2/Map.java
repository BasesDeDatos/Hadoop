import java.io.IOException;
import org.apache.hadoop.mapreduce.Mapper;
import org.apache.hadoop.io.LongWritable;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.io.IntWritable;

public class Map extends Mapper<LongWritable, Text, Text, Text>
{
   protected void map(LongWritable key, Text value, Context ctx)throws IOException, InterruptedException{
		
		String[] line = value.toString().split("<##>");
		String palabra = line[0];
		String direccion = line[1] + "<##>" + line[1];
		ctx.write(new Text(palabra), new Text(direccion));
   }
}
