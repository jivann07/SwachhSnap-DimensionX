import os
import gc

# --- SAFETY MECHANISM 1: Disable Problematic Accelerators ---
os.environ['TF_ENABLE_ONEDNN_OPTS'] = '0'
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '2'

import tensorflow as tf
from tensorflow.keras.preprocessing.image import ImageDataGenerator
from tensorflow.keras.applications import MobileNetV2
from tensorflow.keras.layers import Dense, GlobalAveragePooling2D, Dropout
from tensorflow.keras.models import Model
from tensorflow.keras.optimizers import Adam
from tensorflow.keras.callbacks import ModelCheckpoint, Callback

# --- SAFETY MECHANISM 2: Ultra-Low Batch Size ---
# processing fewer images at once keeps RAM usage low.
BATCH_SIZE = 8  
IMG_SIZE = (224, 224)
DATASET_DIR = "dataset"

print(f"🔄 Connecting to dataset in {DATASET_DIR}...")

# 1. Setup Data Generators
train_datagen = ImageDataGenerator(
    rescale=1./255,
    rotation_range=30,
    width_shift_range=0.2,
    height_shift_range=0.2,
    horizontal_flip=True,
    fill_mode='nearest',
    validation_split=0.2 
)

train_generator = train_datagen.flow_from_directory(
    DATASET_DIR,
    target_size=IMG_SIZE,
    batch_size=BATCH_SIZE,
    class_mode='categorical',
    subset='training'
)

validation_generator = train_datagen.flow_from_directory(
    DATASET_DIR,
    target_size=IMG_SIZE,
    batch_size=BATCH_SIZE,
    class_mode='categorical',
    subset='validation'
)

# 2. Save Labels
labels = (train_generator.class_indices)
labels = dict((v,k) for k,v in labels.items())

print("📝 Saving labels.txt...")
with open("labels.txt", "w") as f:
    for i in range(len(labels)):
        f.write(labels[i] + "\n")

# 3. Build the Brain
base_model = MobileNetV2(weights='imagenet', include_top=False, input_shape=(224, 224, 3))
base_model.trainable = False 

x = base_model.output
x = GlobalAveragePooling2D()(x)
x = Dense(128, activation='relu')(x)
x = Dropout(0.5)(x)
predictions = Dense(len(labels), activation='softmax')(x)

model = Model(inputs=base_model.input, outputs=predictions)

model.compile(optimizer=Adam(learning_rate=0.0001),
              loss='categorical_crossentropy',
              metrics=['accuracy'])

# --- SAFETY MECHANISM 3: Memory Cleaner Callback ---
class MemoryCleaner(Callback):
    def on_epoch_end(self, epoch, logs=None):
        gc.collect()
        tf.keras.backend.clear_session()

# Auto-save best model
checkpoint = ModelCheckpoint("keras_model.h5", monitor='accuracy', verbose=1, save_best_only=False)

# 4. TRAIN!
print("🚀 Starting Robust Training...")
print("ℹ️  Batch Size is 8. This is slow but safe.")

# We increase 'steps_per_epoch' because our batches are smaller
# 500 steps * 8 images = 4,000 images per epoch
history = model.fit(
    train_generator,
    steps_per_epoch=500,  
    epochs=5,
    validation_data=validation_generator,
    validation_steps=50,
    callbacks=[checkpoint, MemoryCleaner()]
)

print("🎉 TRAINING COMPLETE! Your AI is ready.")
model.save("keras_model.h5")