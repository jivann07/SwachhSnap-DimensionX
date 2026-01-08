import sys
import os
import numpy as np
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing import image

# Suppress messy TensorFlow warnings
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'

# 1. Load the Model
try:
    # compile=False avoids errors if the model was made with a slightly different version
    model = load_model("keras_model.h5", compile=False)
    
    # Load Labels (clean up the text)
    with open("labels.txt", "r") as f:
        class_names = [line.strip() for line in f.readlines()]
except Exception as e:
    print(f"Error loading model: {e}")
    sys.exit()

# 2. Get Image Path from PHP
if len(sys.argv) < 2:
    print("No image provided")
    sys.exit()

img_path = sys.argv[1]

# 3. Process Image (Resize to 224x224 for the AI)
try:
    img = image.load_img(img_path, target_size=(224, 224))
    img_array = image.img_to_array(img)
    img_array = np.expand_dims(img_array, axis=0)
    img_array /= 255.0  # Normalize pixel values
except Exception as e:
    print(f"Error processing image: {e}")
    sys.exit()

# 4. Predict
prediction = model.predict(img_array, verbose=0)
index = np.argmax(prediction)
class_name = class_names[index]

# Clean up label (remove "0 " prefix if Teachable Machine added it)
if class_name[0].isdigit():
    class_name = class_name[2:]

print(class_name)