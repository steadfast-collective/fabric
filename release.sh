read -p 'Release: ' releaseName

rm -rf builds/fabric
php fabric app:build fabric --build-version=$releaseName

git add builds/fabric
git commit -m "Build: $releaseName"
git push origin master

git tag -a $releaseName -m $releaseName
git push --tags

echo "Application built and tagged!"
echo "Remember to create release with changelog."
