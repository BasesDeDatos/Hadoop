import java.io.IOException;
import org.apache.hadoop.mapreduce.Reducer;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.io.IntWritable;
import org.apache.hadoop.io.NullWritable;

public class Reduce extends Reducer<Text, IntWritable, DBOutputWritable, NullWritable>
{
   protected void reduce(Text key, Iterable<Text> values, Context ctx)
   {
    int sum = 0;
	for (Text val : values){
		sum++;
	}

	try
	{
		ctx.write(new DBOutputWritable(key.toString(), sum), NullWritable.get());
	} catch(IOException e)
	{
		e.printStackTrace();
	} catch(InterruptedException e)
	{
		e.printStackTrace();
	}
   }
}
