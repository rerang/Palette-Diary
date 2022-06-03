from wordcloud import WordCloud
import matplotlib.pyplot as plt
import numpy as np
from PIL import Image
import json

with open('C:\\test.json', 'r') as file: 
    json_data = json.load(file)
    
keyword = json_data['keyword']
text = ' '.join(keyword)

carrot_mask = np.array(Image.open("carrot.PNG"))

wordcloud = wordCloud(font_path='C:\\Windows\\Fonts\\gulim.ttc', 
	background_color="white", max_font_size=100, mask=carrot_mask).generate(text)

plt.imshow(wordcloud, interpolation='bilinear')
plt.axis('off')
#plt.show()
plt.savefig("wordcloud.png")
